<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Paginator\Adapter;

interface CursorInterface
{
    /**
     * Return the items for pagination as an array representation.
     */
    public function getItems(): array;

    /**
     * Legacy method to render the paginator controls
     * in a partial template.
     */
    public function renderLinks(?string $partial = null): string;

    /**
     * Return the next url for paging after the current cursor.
     */
    public function getNextUrl(): ?string;

    /**
     * Return the previous url for paging before the current cursor.
     */
    public function getPrevUrl(): ?string;

    /**
     * Return the next cursor value.
     */
    public function getNextCursor(): ?int;

    /**
     * Return the previous cursor value.
     */
    public function getPrevCursor(): ?int;

    /**
     * Determine whether paging has commenced.
     */
    public function isFirst(): bool;

    /**
     * Determine whether the resultset is pageable.
     */
    public function isPageable(): bool;

    /**
     * Determine whether paging (before a cursor) is back at the
     * beginning of the resultset.
     */
    public function isRewound(): bool;

    /**
     * Returns the instance as an array representation.
     */
    public function toArray(): array;

    /**
     * Return the json representation of this object.
     *
     * @return bool|string
     */
    public function toJson(int $flags = 0);
}
