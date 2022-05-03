<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Paginator\Cursor;

class Query implements QueryableInterface
{
    public function __construct(
        private int $cursor,
        private bool $before,
        private bool $after
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getCursor(): int
    {
        return $this->cursor;
    }

    /**
     * @inheritDoc
     */
    public function isBefore(): bool
    {
        return $this->before;
    }

    /**
     * @inheritDoc
     */
    public function isAfter(): bool
    {
        return $this->after;
    }

    /**
     * @inheritDoc
     */
    public function isPaging(): bool
    {
        return (0 <> $this->cursor) ?? false;
    }
}
