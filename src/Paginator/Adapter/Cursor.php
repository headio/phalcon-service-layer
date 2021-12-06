<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Paginator\Adapter;

use Headio\Phalcon\ServiceLayer\Paginator\Cursor\QueryableInterface;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Router\RouteInterface;
use JsonSerializable;
use function array_reverse;
use function array_slice;
use function implode;
use function is_null;
use function json_encode;

/**
 * @property \Phalcon\Mvc\RouterInterface $router
 */
class Cursor extends Injectable implements CursorInterface, JsonSerializable
{
    private int $count;

    private ResultsetInterface $resultset;

    private array $items;

    private int $itemsPerPage;

    private bool $pageable;

    private QueryableInterface $query;

    private RouteInterface $route;

    public function __construct(
        ResultsetInterface $resultset,
        int $itemsPerPage,
        QueryableInterface $query
    ) {
        $this->resultset = $resultset;
        $this->itemsPerPage = abs($itemsPerPage);
        $this->query = $query;
        $this->count = $resultset->count();
        $this->pageable = $this->count > $this->itemsPerPage;
        $this->route = $this->router->getMatchedRoute();
        $this->items = array_slice($this->resultset->toArray(), 0, $this->itemsPerPage, true);
    }

    /**
     * @inheritDoc
     */
    public function getItems(): array
    {
        if ($this->query->isBefore()) {
            return array_reverse($this->items, true);
        }

        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function renderLinks(?string $partial = null): string
    {
        return $this->view->getPartial(
            is_null($partial) ? 'partials/cursorPaginatorControls' : $partial,
            [
                'isFirst' => $this->isFirst(),
                'isRewound' => $this->isRewound(),
                'isPageable' => $this->pageable,
                'nextUrl' => $this->getNextUrl(),
                'prevUrl' => $this->getPrevUrl(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getNextUrl(): ?string
    {
        if (is_null($cursor = $this->getNextCursor())) {
            return null;
        }

        /** @var \Phalcon\Collection\CollectionInterface */
        $definition = $this->getDI()
            ->get('config')
            ->paginator->cursor->queryIdentifiers
        ;

        if ($this->isFirst()) {
            $route = implode('/', [
                $this->route->getPattern(),
                $definition->get('after'),
                $cursor
            ]);

            return $this->url->get($route);
        }

        return $this->url->get(
            [
                'for' => $this->route->getName(),
                'cursor' => $cursor,
                'paging' => $definition->get('after'),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getPrevUrl(): ?string
    {
        if (is_null($cursor = $this->getPrevCursor())) {
            return null;
        }

        /** @var \Phalcon\Collection\CollectionInterface */
        $definition = $this->getDI()
            ->get('config')
            ->paginator->cursor->queryIdentifiers
        ;

        return $this->url->get(
            [
                'for' => $this->route->getName(),
                'cursor' => $cursor,
                'paging' => $definition->get('before'),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getNextCursor(): ?int
    {
        if ($this->pageable) {
            if ($this->query->isBefore()) {
                return $this->query->getCursor();
            }

            return $this->resultset->offsetGet($this->itemsPerPage-1)->id;
        }

        return $this->isRewound() ? $this->resultset->getFirst()->id : null;
    }

    /**
     * @inheritDoc
     */
    public function getPrevCursor(): ?int
    {
        if (!$this->query->isPaging()) {
            return null;
        }

        if ($this->query->isAfter()) {
            return $this->query->getCursor();
        }

        return $this->resultset->getLast()->id;
    }

    /**
     * @inheritDoc
     */
    public function isFirst(): bool
    {
        return (!$this->query->isBefore() && !$this->query->isAfter()) ?? false;
    }

    /**
     * @inheritDoc
     */
    public function isPageable(): bool
    {
        return $this->pageable;
    }

    /**
     * @inheritDoc
     */
    public function isRewound(): bool
    {
        return ($this->query->isBefore() && !$this->pageable) ?? false;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $items = $this->items;

        if ($this->query->isBefore()) {
            $items = array_reverse($items, true);
        }

        return [
            'items' => $items,
            'items_per_page' => $this->itemsPerPage,
            'next_url' => $this->getNextUrl(),
            'pageable' => $this->isPageable(),
            'prev_url' => $this->getPrevUrl(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     *
     * @return string|false
     */
    public function toJson(int $flags = 0)
    {
        $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | $flags;

        return json_encode($this->jsonSerialize(), $options);
    }
}
