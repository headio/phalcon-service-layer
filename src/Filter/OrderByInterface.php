<?php
/*
 * This source file is subject to the MIT License.
 *
 * (c) Dominic Beck <dominic@headcrumbs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Filter;

interface OrderByInterface
{
    /**
     * The sort order direction keywords
     * 
     * @var constant
     */
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    /**
     * Return the order by column constraint
     */
    public function getColumn() : string;

    /**
     * Return the order by direction constraint
     */
    public function getDirection() : ?string;

    /**
     * Has an order by direction constraint
     */
    public function hasDirection() : bool;
}
