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

use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Phalcon\Mvc\Model\CriteriaInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;

interface RepositoryInterface
{
    /**
     * Apply the cache to the query criteria.
     */
    public function applyCache(QueryInterface $query, CriteriaInterface $criteria): void;

    /**
     * Apply the filter to the query criteria.
     */
    public function applyFilter(CriteriaInterface $criteria, FilterInterface $filter): void;

    /**
     * Fetch row count from cache or storage.
     */
    public function count(FilterInterface $filter): int;

    /**
     * Return an instance of the query criteria pre-populated
     * with the entity managed by this repository.
     */
    public function createCriteria(): CriteriaInterface;

    /**
     * Return an instance of the query builder pre-populated
     * for the entity managed by this repository.
     */
    public function createQuery(array $params = null, ?string $alias = null): BuilderInterface;

    /**
     * Fetch column value by query criteria.
     *
     * @return mixed
     */
    public function fetchColumn(CriteriaInterface $criteria);

    /**
     * Fetch records by filter criteria from cache or storage.
     */
    public function find(FilterInterface $filter): ResultsetInterface;

    /**
     * Fetch record by primary key from cache or storage.
     */
    public function findByPk(int $id): EntityInterface;

    /**
     * Fetch first record by filter criteria from cache or storage.
     */
    public function findFirst(FilterInterface $filter): EntityInterface;

    /**
     * Fetch first record by property name from cache or storage.
     */
    public function findFirstBy(string $property, $value): EntityInterface;

    /**
     * Return the fully qualified (or unqualified) class name
     * for the entity managed by the repository.
     */
    public function getEntity(bool $unqualified = false): string;

    /**
     * Return the related models from cache or storage.
     */
    public function getRelated(string $alias, EntityInterface $entity, FilterInterface $filter): ResultsetInterface;

    /**
     * Return the unrelated models from cache or storage.
     */
    public function getUnrelated(ResultsetInterface $resultset, FilterInterface $filter): ResultsetInterface;
}
