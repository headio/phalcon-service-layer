<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Module\Admin\Controller;

use Headio\Phalcon\ServiceLayer\Paginator\Cursor\QueryableInterface;
use Phalcon\Mvc\Controller;

/**
 * @RoutePrefix("/tags")
 */
class Tag extends Controller
{
    /**
     * @Paginateable(true)
     *
     * @Get("/", name="adminTags")
     *
     * @Get("/{paging:(prev|next)+}/{cursor:\d+}", name="adminPagingTags")
     */
    public function indexAction(QueryableInterface $cursor)
    {
    }
}
