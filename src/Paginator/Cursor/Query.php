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
    private bool $after;
    private bool $before;
    private int $cursor;

    public function __construct(int $cursor, bool $before, bool $after)
    {
        $this->cursor = $cursor;
        $this->before = $before;
        $this->after = $after;
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
