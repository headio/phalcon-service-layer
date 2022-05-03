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

use Headio\Phalcon\ServiceLayer\Filter\OrderBy;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Paginator\Cursor\QueryableInterface;
use Headio\Phalcon\ServiceLayer\Repository\QueryRepository;
use Stub\Domain\Model\Tag as Model;

/**
 * @property \Phalcon\Http\RequestInterface $request;
 * @property \Phalcon\Mvc\RouterInterface $router;
 */
class Tag extends QueryRepository implements TagInterface
{
    /**
     * {@inheritDoc}
     */
    public function createFilter(QueryableInterface $query, int $limit): CriteriaInterface
    {
        $criteria = $this->createCriteria()->limit($limit+1);

        if (!$query->isPaging()) {
            return $criteria->orderBy('id' . OrderBy::DESC);
        }

        if ($query->isAfter()) {
            $criteria->lt('id', $query->getCursor());
        } else {
            $criteria->gte('id', $query->getCursor());
        }

        $criteria->orderBy('id ' . ($query->isAfter() ? OrderBy::DESC : OrderBy::ASC));

        return $criteria;
    }

    /**
     * Return the model name managed by this repository.
     */
    protected function getModelName(): string
    {
        return Model::class;
    }
}
