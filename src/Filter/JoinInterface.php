<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Filter;

interface JoinInterface
{
    /**
     * The supported join types
     *
     * @var constants
     */
    public const LEFT = 'LEFT';

    public const RIGHT = 'RIGHT';

    public const INNER = 'INNER';

    /**
     * Return the alias used for the target entity constraint.
     */
    public function getAlias(): ?string;

    /**
     * Return the join constraint expression.
     */
    public function getConstraint(): ?string;

    /**
     * Return the fully qualified class name of the target entity.
     */
    public function getEntity(): string;

    /**
     * Rturn the type of join contstraint.
     */
    public function getType(): string;
}
