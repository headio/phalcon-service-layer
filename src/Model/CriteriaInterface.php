<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Model;

use Headio\Phalcon\ServiceLayer\Filter\Condition;
use Phalcon\Mvc\Model\CriteriaInterface as PhalconCriteriaInterface;

interface CriteriaInterface extends PhalconCriteriaInterface
{
    /**
     * Append an equality condition to the query criteria.
     */
    public function eq(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface;

    /**
     * Return the criteria parameters used to form part of a cache key.
     */
    public function getCacheParams(): array;

    /**
     * Append a greater than comparison condition to the filter criteria.
     */
    public function gt(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface;

    /**
     * Append a greater than or equal comparison condition to the filter criteria.
     */
    public function gte(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface;

    /**
     * Append a simple inclusion comparison condition to the filter criteria.
     */
    public function in(string $column, array $values, string $type = Condition::AND): CriteriaInterface;

    /**
     * Append a null value condition to the filter criteria.
     */
    public function isNull(string $column, string $type = Condition::AND): CriteriaInterface;

    /**
     * Append a not null value condition to the filter criteria.
     */
    public function isNotNull(string $column, string $type = Condition::AND): CriteriaInterface;

    /**
     * Append a simple pattern match condition to the filter criteria.
     */
    public function like(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface;

    /**
     * Append a less than comparison condition to the filter criteria.
     */
    public function lt(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface;

    /**
     * Append a less than or equal comparison condition to the filter criteria.
     */
    public function lte(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface;

    /**
     * Append a negation equality condition to the filter criteria.
     */
    public function notEq(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface;

    /**
     * Append a negation pattern match condition to the filter criteria.
     */
    public function notLike(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface;

    /**
     * Append a simple negation inclusion comparison condition to the filter criteria.
     */
    public function notIn(string $column, array $values, string $type = Condition::AND): CriteriaInterface;
}
