<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Filter;

use ArrayIterator;
use function abs;

abstract class Filter implements FilterInterface
{
    private ?string $alias = null;

    private array $columns = [];

    private array $conditions = [];

    private array $groupBy = [];

    private ?int $limit = null;

    private array $offset = [];

    private array $orderBy = [];

    /**
     * {@inheritDoc}
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAlias(): bool
    {
        return !empty($this->alias);
    }

    /**
     * {@inheritDoc}
     */
    public function alias(string $alias): FilterInterface
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * {@inheritDoc}
     */
    public function hasColumns(): bool
    {
        return !empty($this->columns);
    }

    /**
     * {@inheritDoc}
     */
    public function columns(array $columns): FilterInterface
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * {@inheritDoc}
     */
    public function hasLimit(): bool
    {
        return $this->limit > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function limit(int $limit): FilterInterface
    {
        $this->limit = abs($limit);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * {@inheritDoc}
     */
    public function hasGroupBy(): bool
    {
        return !empty($this->groupBy);
    }

    /**
     * {@inheritDoc}
     */
    public function groupBy(array $groupBy): FilterInterface
    {
        foreach ($groupBy as $g) {
            $this->groupBy[] = new GroupBy($g);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * {@inheritDoc}
     */
    public function hasOrderBy(): bool
    {
        return !empty($this->orderBy);
    }

    /**
     * {@inheritDoc}
     */
    public function orderBy(string $column, ?string $direction = null): FilterInterface
    {
        $this->orderBy[] = new OrderBy($column, $direction);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOffset(): array
    {
        return $this->offset;
    }

    /**
     * {@inheritDoc}
     */
    public function hasOffset(): bool
    {
        return !empty($this->offset);
    }

    /**
     * {@inheritDoc}
     *
     * <code>
     *   $filter->offset(5, Filter::LESS_THAN, Condition::AND);
     * </code>
     *
     * The above expression adds the following condition to the filter criteria.
     *
     * <code>
     *   (id < :ID:)
     * </code>
     */
    public function offset(int $offset, string $direction, string $type = Condition::AND): FilterInterface
    {
        $whitelist = [
            Filter::LESS_THAN,
            Filter::LESS_THAN_OR_EQUAL,
            Filter::GREATER_THAN,
            Filter::GREATER_THAN_OR_EQUAL
        ];

        if (!in_array($direction, $whitelist)) {
            $direction = Filter::LESS_THAN;
        }

        $this->offset = [
            abs($offset),
            $direction,
            $type
        ];

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value
     */
    public function addCondition(string $column, $value, string $operator, string $type): void
    {
        $this->conditions[] = new Condition($column, $value, $operator, $type);
    }

    /**
     * {@inheritDoc}
     */
    public function getConditions(): ArrayIterator
    {
        return new ArrayIterator($this->conditions);
    }

    /**
     * {@inheritDoc}
     */
    public function hasConditions(): bool
    {
        return !empty($this->conditions);
    }

    /**
     * {@inheritDoc}
     */
    public function clearConditions(): FilterInterface
    {
        if ($this->hasConditions()) {
            $this->conditions = [];
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value
     */
    public function eq(string $column, $value, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, $value, Filter::EQUAL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value
     */
    public function gt(string $column, $value, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, $value, Filter::GREATER_THAN, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value
     */
    public function gte(string $column, $value, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, $value, Filter::GREATER_THAN_OR_EQUAL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function in(string $column, array $value, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, $value, Filter::IN, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isNull(string $column, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, null, Filter::IS_NULL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isNotNull(string $column, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, null, Filter::IS_NOT_NULL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value
     */
    public function like(string $column, $value, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, '%' . $value . '%', Filter::LIKE, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value
     */
    public function lt(string $column, $value, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, $value, Filter::LESS_THAN, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value
     */
    public function lte(string $column, $value, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, $value, Filter::LESS_THAN_OR_EQUAL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value
     */
    public function notEq(string $column, $value, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, $value, Filter::NOT_EQUAL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value
     */
    public function notLike(string $column, $value, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, '%' . $value . '%', Filter::NOT_LIKE, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function notIn(string $column, array $value, string $type = Condition::AND): FilterInterface
    {
        $this->addCondition($column, $value, Filter::NOT_IN, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): FilterInterface
    {
        if ($this->hasColumns()) {
            $this->columns = [];
        }

        if ($this->hasConditions()) {
            $this->conditions = [];
        }

        if ($this->hasGroupBy()) {
            $this->groupBy = [];
        }

        if ($this->hasOrderBy()) {
            $this->orderBy = [];
        }

        $this->limit = null;
        $this->offset = [];
        $this->alias = null;

        return $this;
    }
}
