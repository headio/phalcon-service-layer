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

namespace Headio\Phalcon\ServiceLayer\Filter;

use ArrayIterator;

interface FilterInterface
{
    /**
     * The condition filters
     *
     * @var constants
     */
    public const EQUAL = '=';
    public const GREATER_THAN = '>';
    public const GREATER_THAN_OR_EQUAL = '>=';
    public const IN = 'IN';
    public const NOT_IN = 'NOT IN';
    public const LESS_THAN = '<';
    public const LESS_THAN_OR_EQUAL = '<=';
    public const LIKE = 'LIKE';
    public const NOT_LIKE = 'NOT LIKE';
    public const NOT_EQUAL = '<>';
    public const IS_NULL = 'IS NULL';
    public const IS_NOT_NULL = 'IS NOT NULL';

    /**
     * Return the alias
     */
    public function getAlias() : ?string;

    /**
     * Has an alias
     */
    public function hasAlias() : bool;

    /**
     * Prepend an alias to the query columns
     */
    public function alias(string $alias) : FilterInterface;

    /**
     * Return the columns constraint
     */
    public function getColumns() : array;

    /**
     * Has a columns constraint
     */
    public function hasColumns() : bool;

    /**
     * Replace the select all expression with explicit columns.
     */
    public function columns(array $columns) : FilterInterface;

    /**
     * Return the limit constraint
     */
    public function getLimit() : ?int;

    /**
     * Has a limit constraint
     */
    public function hasLimit() : bool;

    /**
     * Add a limit constraint to the filter criteria.
     */
    public function limit(int $limit) : FilterInterface;

    /**
     * Return the group by constraint
     */
    public function getGroupBy() : array;

    /**
     * Has a group by constraint
     */
    public function hasGroupBy() : bool;

    /**
     * Add a group by constraint to the filter criteria.
     */
    public function groupBy(array $groupBy) : FilterInterface;

    /**
     * Return the order by constraint
     */
    public function getOrderBy() : array;

    /**
     * Has an order by constraint
     */
    public function hasOrderBy() : bool;

    /**
     * Add an order by constraint to the filter criteria.
     */
    public function orderBy(string $column, ?string $direction = null) : FilterInterface;

    /**
     * Return the offset constraint
     */
    public function getOffset() : array;

    /**
     * Has an offset constraint
     */
    public function hasOffset() : bool;

    /**
     * Add an offset condition constraint to the filter criteria.
     */
    public function offset(int $offset, string $direction, string $type = Condition::AND)  : FilterInterface;

    /**
     * Append a condition to the filter criteria.
     */
    public function addCondition(string $column, $value, string $operator, string $type) : void;

    /**
     * Return the conditions constraint
     */
    public function getConditions() : ArrayIterator;

    /**
     * Has a conditions constraint
     */
    public function hasConditions(): bool;

    /**
     * Clear the conditions constraint
     */
    public function clearConditions(): FilterInterface;

    /**
     * Append an equality condition to the filter criteria.
     */
    public function eq(string $column, $value, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a greater than comparison condition to the filter criteria.
     */
    public function gt(string $column, $value, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a great than or equal comparison condition to the filter criteria.
     */
    public function gte(string $column, $value, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a simple inclusion comparison condition to the filter criteria.
     */
    public function in(string $column, array $value, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a null value condition to the filter criteria.
     */
    public function isNull(string $column, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a not null value condition to the filter criteria.
     */
    public function isNotNull(string $column, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a simple pattern match condition to the filter criteria.
     */
    public function like(string $column, $value, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a less than comparison condition to the filter criteria.
     */
    public function lt(string $column, $value, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a less than or equal comparison condition to the filter criteria.
     */
    public function lte(string $column, $value, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a nagation equality condition to the filter criteria.
     */
    public function notEq(string $column, $value, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a negation pattern match condition to the filter criteria.
     */
    public function notLike(string $column, $value, string $type = Condition::AND) : FilterInterface;

    /**
     * Append a simple negation inclusion comparison condition to the filter criteria.
     */
    public function notIn(string $column, array $value, string $type = Condition::AND) : FilterInterface;

    /**
     * Reset the filter criteria
     */
    public function clear(): FilterInterface;
}
