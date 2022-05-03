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
use Phalcon\Support\Helper\Str\Upper;
use Phalcon\Mvc\Model\Criteria as PhalconCriteria;
use ARRAY_FILTER_USE_KEY;
use function array_filter;
use function sprintf;
use function strcasecmp;

class Criteria extends PhalconCriteria implements CriteriaInterface
{
    private int $key = 0;

    /**
     * {@inheritDoc}
     */
    public function eq(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, $value, Condition::EQUAL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCacheParams(): array
    {
        $params = array_filter(
            $this->params,
            fn ($k) => $k <> 'di' && $k <> 'bindTypes',
            ARRAY_FILTER_USE_KEY,
        );

        return $params;
    }

    /**
     * {@inheritDoc}
     */
    public function gt(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, $value, Condition::GREATER_THAN, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function gte(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, $value, Condition::GREATER_THAN_OR_EQUAL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function in(string $column, array $values, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, $values, Condition::IN, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isNull(string $column, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, null, Condition::IS_NULL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isNotNull(string $column, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, null, Condition::IS_NOT_NULL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function like(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, '%' . $value . '%', Condition::LIKE, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function lt(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, $value, Condition::LESS_THAN, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function lte(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, $value, Condition::LESS_THAN_OR_EQUAL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function notEq(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, $value, Condition::NOT_EQUAL, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function notLike(string $column, mixed $value, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, '%' . $value . '%', Condition::NOT_LIKE, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function notIn(string $column, array $values, string $type = Condition::AND): CriteriaInterface
    {
        $this->addCondition($column, $values, Condition::NOT_IN, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function addCondition(string $column, mixed $value, string $operator, string $type): void
    {
        if (0 !== strcasecmp($type, Condition::AND)) {
            $type = Condition::OR;
        }

        $placeholder = (new Upper())($column). $this->key;
        $expression = $this->buildConditionExpression(
            $column,
            $operator,
            $placeholder,
        );
        $model = $this->getModelName();
        $bindType = (new $model())->getPropertyBindType($column);
        $callable = "{$type}Where";
        $this->$callable(
            $expression,
            [$placeholder => $value],
            [$placeholder => $bindType],
        );
        $this->key++;
    }

    /**
     * Build the phalcon sql expression for the condition constraint.
     */
    protected function buildConditionExpression(
        string $column,
        string $operator,
        string $placeholder
    ): string {
        $format = match ($operator) {
            Condition::IN => '%1$s IN ({%2$s:array})',
            Condition::IS_NULL => '%1$s IS NULL',
            Condition::IS_NOT_NULL => '%1$s IS NOT NULL',
            Condition::LIKE => '%1$s LIKE LOWER(:%2$s:)',
            Condition::NOT_IN => '%1$s NOT IN ({%2$s:array})',
            Condition::NOT_LIKE => '%1$s NOT LIKE LOWER(:%2$s:)',
            default => '%1$s ' . $operator . ' :%2$s:',
        };

        return sprintf($format, $column, $placeholder);
    }
}
