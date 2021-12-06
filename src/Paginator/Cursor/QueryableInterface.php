<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Paginator\Cursor;

interface QueryableInterface
{
    /**
     * Return the cursor value from the request context.
     */
    public function getCursor(): int;

    /**
     * Determine whether the request is paging after the
     * cursor value.
     */
    public function isAfter(): bool;

    /**
     * Determine whether the request is paging before the
     * cursor value.
     */
    public function isBefore(): bool;

    /**
     * Determine whether the request is currently paging.
     */
    public function isPaging(): bool;
}
