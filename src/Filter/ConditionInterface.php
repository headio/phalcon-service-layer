<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Filter;

interface ConditionInterface
{
    /**
     * The condition types
     *
     * @var constants
     */
    public const AND = 'AND';

    public const OR = 'OR';

    /**
     * Return the condition column
     */
    public function getColumn(): string;

    /**
     * Return the condition operator
     */
    public function getOperator(): string;

    /**
     * Return the condition type
     */
    public function getType(): string;

    /**
     * Return the condition value
     *
     * @return mixed
     */
    public function getValue();
}
