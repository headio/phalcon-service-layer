<?php
/*
 * This source file is subject to the MIT License.
 *
 * (c) Dominic Beck <dominic@headcrumbs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Repository;

use Headio\Phalcon\ServiceLayer\Exception;
use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Headio\Phalcon\ServiceLayer\Filter\ConditionInterface;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Headio\Phalcon\ServiceLayer\Helper\Inflector;
use Phalcon\Mvc\Model\CriteriaInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Row;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use Phalcon\Di\Injectable;
use function class_exists;
use function current;
use function get_class;
use function is_null;
use function sprintf;
use function strpos;
use function strrpos;
use function strtolower;
use function substr;

/**
 * A generic abstract query repository class.
 */
abstract class QueryRepository extends Injectable implements RepositoryInterface
{
    /**
     * An array representation of query criteria binding parameter types.
     *
     * @var array
     */
    protected $bindTypes = [];

    /**
     * Is the repository using caching across all queries.
     *
     * @var bool
     */
    protected $cache;

    public function __construct(bool $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Magic method to handle calls to undefined methods or
     * inaccessible methods and possible eventual delegation.
     *
     * @return mixed
     * @throws Exception\BadMethodCallException
     */
    public function __call(string $method, array $args)
    {
        switch (true) {
            case (0 === strpos($method, 'findFirstBy')):
                $prop = strtolower(substr($method, 11));
                return $this->findFirstBy($prop, ...$args);
                break;
            case (0 === strpos($method, 'getRelated')):
                $prop = Inflector::variablize(substr($method, 10));
                return $this->getRelated($prop, ...$args);
                break;
            default:
                throw new Exception\BadMethodCallException(
                    sprintf('Repository method %s not implemented.', $method),
                    405
                );
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function applyCache(QueryInterface $query, CriteriaInterface $criteria) : void
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
    public function applyFilter(CriteriaInterface $criteria, FilterInterface $filter) : void
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

        if ($filter->hasConditions()) {
            if ($filter->hasAlias()) {
                $alias = $filter->getAlias();
            } else {
                $alias = (null === $criteria->getModelName() ? $entityName : null);
            }
            $collection = $filter->getConditions();
            $collection->rewind();
            while ($collection->valid()) {
                $placeholder = strtoupper($collection->current()->getColumn()) . $collection->key();
                $expression = $this->buildConditionExpression($collection->current(), $placeholder, $alias);
                $callable = $collection->current()->getType() . 'Where';
                $criteria->{$callable}($expression);
                if (!is_null($collection->current()->getValue())) {
                    $criteria->bind([$placeholder => $collection->current()->getValue()], true);
                    $this->bindTypes[$placeholder] = (new $entityName)->getPropertyBindType($collection->current()->getColumn());
                }
                $collection->next();
            }
            if (!empty($this->bindTypes)) {
                $criteria->bindTypes($this->bindTypes);
            }
        }

        if ($filter->hasGroupBy()) {
            foreach ($filter->getGroupBy() as $item) {
                if ($filter->hasAlias()) {
                    $groupBy[] = sprintf('%s.%s', $filter->getAlias(), $item->getColumn());
                } else {
                    $groupBy[] = $item->getColumn();
                }
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
                if ($filter->hasAlias()) {
                    $orderBy[] = sprintf('%s.%s', $filter->getAlias(), $format);
                } else {
                    $orderBy[] = $format;
                }
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
    public function count(FilterInterface $filter) : int
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
    public function createCriteria() : CriteriaInterface
    {
        $entity = $this->getEntity();

        return $entity::Query();
    }

    /**
     * {@inheritDoc}
     */
    public function createQuery(array $params = null, ?string $alias = null) : BuilderInterface
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
    public function find(FilterInterface $filter) : ResultsetInterface
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
    public function findByPk(int $id) : EntityInterface
    {
        $entity = $this->getEntity();
        $filter = $this->getQueryFilter()->eq((new $entity)->getPrimaryKey(), $id);

        return $this->findFirst($filter);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\NotFoundException
     */
    public function findFirst(FilterInterface $filter) : EntityInterface
    {
        $criteria = $this->createCriteria();
        $this->applyFilter($criteria, $filter);
        $query = $criteria->createBuilder()->getQuery()->setUniqueRow(true);

        if ($this->cache) {
            $this->applyCache($query, $criteria);
        }

        $model = $query->execute();

        if (!$model instanceof EntityInterface) {
            throw new Exception\NotFoundException('404 Not Found');
        }

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findFirstBy(string $property, $value) : EntityInterface
    {
        $filter = $this->getQueryFilter()->eq($property, $value);

        return $this->findFirst($filter);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\InvalidArgumentException
     */
    public function getEntity(bool $unqualified = false) : string
    {
        $fqcn = $this->getEntityName();

        if (!class_exists($fqcn)) {
            throw new Exception\InvalidArgumentException(
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
     * @throws Exception\OutOfRangeException
     */
    public function getRelated(string $alias, EntityInterface $entity, FilterInterface $filter) : ResultsetInterface
    {
        $entityName = get_class($entity);
        /**
         * Stop execution if the alias is unknown.
         */
        if (false === $this->modelsManager->getRelationByAlias($entityName, $alias)) {
            throw new Exception\OutOfRangeException(
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
            return $entity->getRelated($alias, $criteria->getParams());
        }

        return $this->cacheManager->fetch(
            $this->cacheManager->createKey(
                $entityName,
                ['id' => $entity->id, 'rel' => $alias] + $criteria->getParams()
            ),
            function () use ($entity, $alias, $criteria) {
                return $entity->getRelated($alias, $criteria->getParams());
            }
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getUnrelated(ResultsetInterface $resultset, FilterInterface $filter) : ResultsetInterface
    {
        if ($resultset->count() <> 0) {
            $keys = [];
            $resultset->rewind();
            while ($resultset->valid()) {
                $keys[] = $resultset->current()->id;
                $resultset->next();
            }

            $entityName = $this->getEntityName();
            $filter->notIn((new $entityName)->getPrimaryKey(), $keys);
        }

        return $this->find($filter);
    }

    /**
     * Return a condition expression in phalcon's `phql` syntax.
     */
    private function buildConditionExpression(ConditionInterface $condition, string $placeholder, ?string $alias = null) : string
    {
        $useAlias = !empty($alias) ?? false;

        switch ($operator = $condition->getOperator()) {
            case FilterInterface::LIKE:
                $format = '%1$s LIKE LOWER(:%2$s:)';
                if ($useAlias) {
                    $format = '[%1$s].[%2$s] LIKE LOWER(:%3$s:)';
                }
                break;
            case FilterInterface::IN:
                $format = '%1$s IN ({%2$s:array})';
                if ($useAlias) {
                    $format = '[%1$s].[%2$s] IN ({%3$s:array})';
                }
                break;
            case FilterInterface::NOT_IN:
                $format = '%1$s NOT IN ({%2$s:array})';
                if ($useAlias) {
                    $format = '[%1$s].[%2$s] NOT IN ({%3$s:array})';
                }
                break;
            case FilterInterface::NOT_LIKE:
                $format = '%1$s NOT LIKE LOWER(:%2$s:)';
                if ($useAlias) {
                    $format = '[%1$s].[%2$s] NOT LIKE LOWER(:%3$s:)';
                }
                break;
            case FilterInterface::IS_NULL:
                $format = '%1$s IS NULL';
                if ($useAlias) {
                    $format = '[%1$s].[%2$s] IS NULL';
                }
                break;
            case FilterInterface::IS_NOT_NULL:
                $format = '%1$s IS NOT NULL';
                if ($useAlias) {
                    $format = '[%1$s].[%2$s] IS NOT NULL';
                }
                break;
            default:
                $format = '%1$s ' . $operator . ' :%2$s:';
                if ($useAlias) {
                    $format = '[%1$s].[%2$s] ' . $operator . ' :%3$s:';
                }
                break;
        }

        if ($useAlias) {
            return sprintf($format, $alias, $condition->getColumn(), $placeholder);
        }

        return sprintf($format, $condition->getColumn(), $placeholder);
    }

    /**
     * Return the fully qualified class name for the entity managed by the repository.
     */
    abstract protected function getEntityName() : string;

    /**
     * Return the query filter to be used with the repository.
     */
    abstract public function getQueryFilter() : FilterInterface;
}
