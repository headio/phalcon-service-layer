<?php
/**
 * This source file is subject to the MIT License.
 *
 * (c) Dominic Beck <dominic@headcrumbs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Repository;

use Stub\Domain\Entity\Tag as EntityName;
use Stub\Domain\Filter\Tag as QueryFilter;
use Headio\Phalcon\ServiceLayer\Paginator\Cursor\QueryableInterface;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Headio\Phalcon\ServiceLayer\Filter\OrderByInterface;
use Headio\Phalcon\ServiceLayer\Repository\QueryRepository;

/**
 * @property \Phalcon\Http\RequestInterface $request;
 * @property \Phalcon\Mvc\RouterInterface $router;
 */
class Tag extends QueryRepository implements TagInterface
{
    /**
     * {@inheritDoc}
     */
    public function createFilter(?QueryableInterface $query, int $limit): FilterInterface
    {
        $filter = $this->getQueryFilter()->limit($limit+1);

        if (!$query->isPaging()) {
            return $filter->orderBy('id', OrderByInterface::DESC);
        }

        $filter->offset(
            $query->getCursor(),
            $query->isAfter() ? FilterInterface::LESS_THAN : FilterInterface::GREATER_THAN_OR_EQUAL
        )
        ->orderBy('id', $query->isAfter() ? OrderByInterface::DESC : OrderByInterface::ASC);

        return $filter;
    }

    /**
     * Return an instance of the query filter used with this repository.
     */
    public function getQueryFilter(): FilterInterface
    {
        return new QueryFilter();
    }

    /**
     * Return the entity name managed by this repository.
     */
    public function getEntityName(): string
    {
        return EntityName::class;
    }
}
