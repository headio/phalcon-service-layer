<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Repository;

use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Headio\Phalcon\ServiceLayer\Exception\BadMethodCallException;
use Headio\Phalcon\ServiceLayer\Exception\NotFoundException;
use Headio\Phalcon\ServiceLayer\Exception\OutOfRangeException;
use Headio\Phalcon\ServiceLayer\Exception\InvalidArgumentException;
use Headio\Phalcon\ServiceLayer\Filter\ConditionInterface;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Phalcon\Mvc\Model\CriteriaInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Row;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use Phalcon\Di\Injectable;
use Phalcon\Support\Helper\Str\Lower;
use function class_exists;
use function current;
use function is_null;
use function sprintf;
use function strpos;
use function strrpos;
use function strtolower;
use function substr;

/**
 * A generic abstract query repository class.
 *
 * @property \Headio\Phalcon\ServiceLayer\Component\CacheManager $cacheManager
 * @property \Phalcon\Mvc\Model\ManagerInterface $modelsManager
 */
abstract class QueryRepository extends Injectable implements RepositoryInterface
{
    /**
     * An array representation of query criteria binding parameter types.
     */
    protected array $bindTypes = [];

    /**
     * Is the repository using caching across all queries.
     */
    protected bool $cache;

    public function __construct(bool $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Magic method to handle calls to undefined methods or
     * inaccessible methods and possible eventual delegation.
     *
     * @throws BadMethodCallException
     */
    public function __call(string $method, array $args): EntityInterface|ResultsetInterface|bool|int
    {
        switch (true) {
            case (0 === strpos($method, 'findFirstBy')):
                $prop = strtolower(substr($method, 11));

                return $this->findFirstBy($prop, ...$args);
            case (0 === strpos($method, 'getRelated')):
                $prop = substr($method, 10);
                $prop = (new Lower())($prop[0]) . substr($prop, 1);

                return $this->getRelated($prop, ...$args);
            default:
                throw new BadMethodCallException(
                    sprintf('Repository method %s not implemented.', $method),
                    405
                );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function applyCache(QueryInterface $query, CriteriaInterface $criteria): void
    {
        $cacheParams = $this->cacheManager->createCacheParameters(
            $this->getEntity(),
            $criteria->getParams()
        );
        $query->cache($cacheParams);
    }

    /**
     * {@inheritDoc}
     */
    public function applyFilter(CriteriaInterface $criteria, FilterInterface $filter): void
    {
        $entityName = $this->getEntity();

        if ($filter->hasColumns()) {
            $criteria->columns($filter->getColumns());
        }

        if ($filter->hasOffset()) {
            $filter->addCondition(
                (new $entityName())->getPrimaryKey(),
                ...$filter->getOffset()
            );
        }

        if ($filter->hasJoins()) {
            $collection = $filter->getJoins();
            $collection->rewind();
            while ($collection->valid()) {
                /** @var \Headio\Phalcon\ServiceLayer\Filter\JoinInterface */
                $join = $collection->current();
                $criteria->join(
                    $join->getEntity(),
                    $join->getConstraint(),
                    $join->getAlias(),
                    $join->getType(),
                );
                $collection->next();
            }
        }

        if ($filter->hasConditions()) {
            $collection = $filter->getConditions();
            $collection->rewind();

            while ($collection->valid()) {
                /** @var ConditionInterface */
                $condition = $collection->current();
                $placeholder = strtoupper($condition->getColumn()) . $collection->key();
                $expression = $this->buildConditionExpression($condition, $placeholder);
                $callable = $collection->current()->getType() . 'Where';
                $criteria->{$callable}($expression);

                if (!is_null($condition->getValue())) {
                    $criteria->bind([$placeholder => $condition->getValue()], true);
                    $this->bindTypes[$placeholder] = (new $entityName())->getPropertyBindType($condition->getColumn());
                }

                $collection->next();
            }

            if (!empty($this->bindTypes)) {
                $criteria->bindTypes($this->bindTypes);
            }
        }

        if ($filter->hasGroupBy()) {
            foreach ($filter->getGroupBy() as $item) {
                $groupBy[] = $item->getColumn();
            }

            $criteria->groupBy(join(',', $groupBy));
        }

        if ($filter->hasOrderBy()) {
            foreach ($filter->getOrderBy() as $item) {
                if ($item->hasDirection()) {
                    $format = sprintf('%s %s', $item->getColumn(), $item->getDirection());
                } else {
                    $format = $item->getColumn();
                }

                $orderBy[] = $format;
            }

            $criteria->orderBy(join(',', $orderBy));
        }

        if ($filter->hasLimit()) {
            $criteria->limit($filter->getLimit());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function count(FilterInterface $filter): int
    {
        $criteria = $this
            ->createCriteria()
            ->columns(['c' => 'COUNT(*)']);
        $this->applyFilter($criteria, $filter);

        return (int) $this->fetchColumn($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function createCriteria(): CriteriaInterface
    {
        $entityName = $this->getEntity();

        return $entityName::Query();
    }

    /**
     * {@inheritDoc}
     */
    public function createQuery(array $params = null, ?string $alias = null): BuilderInterface
    {
        $queryBuilder = $this->modelsManager->createBuilder($params);

        if (isset($params['models'])) {
            return $queryBuilder;
        }

        if (!empty($alias)) {
            $queryBuilder->from([$alias => $this->getEntity()]);
        } else {
            $queryBuilder->from($this->getEntity());
        }

        return $queryBuilder;
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function fetchColumn(CriteriaInterface $criteria)
    {
        $query = $criteria->createBuilder()->getQuery();

        if ($this->cache) {
            $this->applyCache($query, $criteria);
        }

        $resultset = $query->getSingleResult();

        if ($resultset instanceof Row) {
            return current($resultset->toArray());
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function find(FilterInterface $filter): ResultsetInterface
    {
        $criteria = $this->createCriteria();
        $this->applyFilter($criteria, $filter);
        $query = $criteria->createBuilder()->getQuery();

        if ($this->cache) {
            $this->applyCache($query, $criteria);
        }

        return $query->execute();
    }

    /**
     * {@inheritDoc}
     */
    public function findByPk(int $id): EntityInterface
    {
        $entityName = $this->getEntity();
        $filter = $this
            ->getQueryFilter()
            ->eq(
                (new $entityName())->getPrimaryKey(),
                $id
            )
        ;

        return $this->findFirst($filter);
    }

    /**
     * {@inheritDoc}
     *
     * @throws NotFoundException
     */
    public function findFirst(FilterInterface $filter): EntityInterface
    {
        $criteria = $this->createCriteria();
        $this->applyFilter($criteria, $filter);
        $query = $criteria->createBuilder()->getQuery()->setUniqueRow(true);

        if ($this->cache) {
            $this->applyCache($query, $criteria);
        }

        $model = $query->execute();

        if (!$model instanceof EntityInterface) {
            throw new NotFoundException('404 Not Found');
        }

        return $model;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value
     */
    public function findFirstBy(string $property, $value): EntityInterface
    {
        $filter = $this->getQueryFilter()->eq($property, $value);

        return $this->findFirst($filter);
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException
     */
    public function getEntity(bool $unqualified = false): string
    {
        $fqcn = $this->getEntityName();

        if (!class_exists($fqcn)) {
            throw new InvalidArgumentException(
                sprintf('Entity %s does not exist.', $fqcn)
            );
        }

        if ($unqualified) {
            return substr($fqcn, strrpos($fqcn, '\\') + 1);
        }

        return $fqcn;
    }

    /**
     * {@inheritDoc}
     *
     * @throws OutOfRangeException
     */
    public function getRelated(
        string $alias,
        EntityInterface $model,
        FilterInterface $filter,
    ): ResultsetInterface|bool|int {
        $entityName = $model::class;
        $manager = $this->modelsManager;
        /**
         * Stop execution if the alias is unknown.
         */
        if (false === ($relation = $manager->getRelationByAlias(
            $entityName,
            $alias
        ))) {
            throw new OutOfRangeException(
                sprintf(
                    "Missing alias '%s' in '%s' entity relationship definition.",
                    $alias,
                    $entityName
                )
            );
        }

        $criteria = $this->createCriteria();
        $this->applyFilter($criteria, $filter);

        if (!$this->cache) {
            return $manager->getRelationRecords($relation, $model, $criteria->getParams());
        }

        return $this->cacheManager->fetch(
            $this->cacheManager->createKey(
                $entityName,
                ['id' => $model->getId(), 'rel' => $alias] + $criteria->getParams()
            ),
            function () use ($criteria, $manager, $model, $relation): ResultsetInterface|bool|int {
                return $manager->getRelationRecords($relation, $model, $criteria->getParams());
            }
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getUnrelated(
        ResultsetInterface $resultset,
        FilterInterface $filter,
    ): ResultsetInterface {
        if ($resultset->count() <> 0) {
            $keys = [];
            $resultset->rewind();
            while ($resultset->valid()) {
                /** @var array<int,int> */
                $keys[] = $resultset->current()->id;
                $resultset->next();
            }

            $entityName = $this->getEntity();
            $filter->notIn((new $entityName())->getPrimaryKey(), $keys);
        }

        return $this->find($filter);
    }

    /**
     * Return a condition expression in phalcon's `phql` syntax.
     */
    private function buildConditionExpression(ConditionInterface $condition, string $placeholder): string
    {
        switch ($operator = $condition->getOperator()) {
            case FilterInterface::LIKE:
                $format = '%1$s LIKE LOWER(:%2$s:)';

                break;
            case FilterInterface::IN:
                $format = '%1$s IN ({%2$s:array})';

                break;
            case FilterInterface::NOT_IN:
                $format = '%1$s NOT IN ({%2$s:array})';

                break;
            case FilterInterface::NOT_LIKE:
                $format = '%1$s NOT LIKE LOWER(:%2$s:)';

                break;
            case FilterInterface::IS_NULL:
                $format = '%1$s IS NULL';

                break;
            case FilterInterface::IS_NOT_NULL:
                $format = '%1$s IS NOT NULL';

                break;
            default:
                $format = '%1$s ' . $operator . ' :%2$s:';

                break;
        }

        return sprintf($format, $condition->getColumn(), $placeholder);
    }

    /**
     * Return the fully qualified class name for the entity managed by the repository.
     */
    abstract protected function getEntityName(): string;

    /**
     * Return the query filter to be used with the repository.
     */
    abstract public function getQueryFilter(): FilterInterface;
}
