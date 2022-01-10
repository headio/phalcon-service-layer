<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Filter;

use function strtoupper;
use function strcmp;

class Condition implements ConditionInterface
{
    /**
     * @param mixed $value
     */
    public function __construct(
        private string $column,
        private $value,
        private string $operator,
        private string $type = ConditionInterface::AND,
    ) {
        if (0 !== strcmp($this->type, ConditionInterface::AND)) {
            $this->type = ConditionInterface::OR;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * {@inheritDoc}
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
