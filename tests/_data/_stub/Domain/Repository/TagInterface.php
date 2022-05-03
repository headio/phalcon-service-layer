<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Repository;

use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Paginator\Cursor\QueryableInterface;

interface TagInterface
{
    public function createFilter(QueryableInterface $query, int $limit): CriteriaInterface;
}
