<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Repository;

use Headio\Phalcon\ServiceLayer\Exception\BadMethodCallException;
use Headio\Phalcon\ServiceLayer\Exception\NotFoundException;
use Headio\Phalcon\ServiceLayer\Exception\OutOfRangeException;
use Headio\Phalcon\ServiceLayer\Exception\InvalidArgumentException;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Headio\Phalcon\ServiceLayer\Paginator\Adapter\Cursor;
use Headio\Phalcon\ServiceLayer\Paginator\Adapter\CursorInterface;
use Headio\Phalcon\ServiceLayer\Paginator\Cursor\QueryableInterface;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Row;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use Phalcon\Support\Helper\Str\Decapitalize;
use Phalcon\Support\Helper\Str\Uncamelize;

use function class_exists;
use function current;
use function sprintf;
use function strrpos;
use function substr;

/**
 * A generic abstract query repository providing concrete
 * query methods.
 *
 * @property \Phalcon\Mvc\Model\ManagerInterface $modelsManager
 */
abstract class QueryRepository extends Injectable implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createCriteria(): CriteriaInterface
    {
        $model = $this->getModel();

        return $model::query(
            $this->getDI()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBuilder(array $params = null): BuilderInterface
    {
        $builder = $this->modelsManager->createBuilder($params);

        return $builder;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchColumn(CriteriaInterface $criteria): mixed
    {
        $query = $criteria->createBuilder()->getQuery();
        $resultset = $query->getSingleResult();

        if ($resultset instanceof Row) {
            return current($resultset->toArray());
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function find(CriteriaInterface $criteria): ResultsetInterface
    {
        $query = $criteria->createBuilder()->getQuery();

        return $query->execute();
    }

    /**
     * {@inheritDoc}
     */
    public function findByPk(int $id): ModelInterface
    {
        $model = $this->getModel();
        $criteria = $this
            ->createCriteria()
            ->eq((new $model())->getPrimaryKey(), $id)
        ;

        return $this->findFirst($criteria);
    }

    /**
     * {@inheritDoc}
     *
     * @throws NotFoundException
     */
    public function findFirst(CriteriaInterface $criteria): ModelInterface
    {
        $query = $criteria
            ->createBuilder()
            ->getQuery()
            ->setUniqueRow(true)
        ;
        $model = $query->execute();

        if (!$model instanceof ModelInterface) {
            throw new NotFoundException('404 Not Found');
        }

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findFirstBy(string $property, mixed $value): ModelInterface
    {
        $criteria = $this
            ->createCriteria()
            ->eq($property, $value)
        ;

        return $this->findFirst($criteria);
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException
     */
    public function getModel(bool $unqualified = false): string
    {
        if (!class_exists($fqcn = $this->getModelName())) {
            throw new InvalidArgumentException(
                sprintf('Class "%s" does not exist.', $fqcn)
            );
        }

        if ($unqualified) {
            return substr($fqcn, strrpos($fqcn, '\\') + 1);
        }

        return $fqcn;
    }

    /**
     * {@inheritDoc}
     */
    public function paginateWithCursor(
        QueryableInterface $query,
        CriteriaInterface $criteria,
        int $limit,
    ): CursorInterface {
        $models = $this->find($criteria);

        return new Cursor($models, $limit, $query);
    }

    /**
     * Handle calls to inaccessible (or undefined) methods
     * and eventual delegation.
     *
     * @throws BadMethodCallException
     */
    public function __call(string $method, array $args): mixed
    {
        if (str_starts_with($method, 'findFirstBy')) {
            $prop = (new Uncamelize())(substr($method, 11));

            return $this->findFirstBy($prop, ...$args);
        }

        if (str_starts_with($method, 'get')) {
            $prop = (new Decapitalize())(substr($method, 3));

            return $this->getRelated($prop, ...$args);
        }

        if (str_starts_with($method, 'count')) {
            $prop = (new Decapitalize())(substr($method, 5));
            $params = [
                'model' => $args[0],
                'method' => 'count',
            ];
            $params['criteria'] = $args[1]??= null;

            return $this->getRelated($prop, ...$params);
        }

        throw new BadMethodCallException(
            sprintf('Repository method "%s" not implemented.', $method),
            405
        );
    }

    /**
     * Return a collection of related models by query criteria or the
     * number of related records from storage.
     *
     * @throws OutOfRangeException
     */
    protected function getRelated(
        string $alias,
        ModelInterface $model,
        CriteriaInterface $criteria = null,
        string $method = null,
    ): ResultsetInterface|ModelInterface|bool|int {
        $modelName = $model::class;
        $manager = $this->modelsManager;
        /**
         * Stop execution if the alias is unknown.
         */
        if (false === ($relation = $manager->getRelationByAlias(
            $modelName,
            $alias
        ))) {
            throw new OutOfRangeException(
                sprintf(
                    'Missing alias "%s" in "%s" model relationship definition.',
                    $alias,
                    $modelName
                )
            );
        }

        $criteria ??= $this->createCriteria();
        $params = $criteria->getParams();

        return $manager->getRelationRecords($relation, $model, $params, $method);
    }

    /**
     * Return the fully qualified class name of the model managed
     * by this repository.
     */
    abstract protected function getModelName(): string;
}
