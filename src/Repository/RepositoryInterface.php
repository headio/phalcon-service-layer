<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Repository;

use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;

interface RepositoryInterface
{
    /**
     * Return an instance of the query criteria pre-populated
     * with the model managed by this repository.
     */
    public function createCriteria(): CriteriaInterface;

    /**
     * Return an instance of the query builder.
     */
    public function createBuilder(array $params = null): BuilderInterface;

    /**
     * Fetch a column value by query criteria from storage.
     */
    public function fetchColumn(CriteriaInterface $criteria): mixed;

    /**
     * Fetch records by query criteria from storage.
     */
    public function find(CriteriaInterface $criteria): ResultsetInterface;

    /**
     * Fetch record by primary key from storage.
     */
    public function findByPk(int $id): ModelInterface;

    /**
     * Fetch first record by query criteria from storage.
     */
    public function findFirst(CriteriaInterface $criteria): ModelInterface;

    /**
     * Fetch first record by property name from storage.
     */
    public function findFirstBy(string $property, mixed $value): ModelInterface;

    /**
     * Return the fully qualified (or unqualified) class name
     * for the model managed by this repository.
     */
    public function getModel(bool $unqualified = false): string;
}
