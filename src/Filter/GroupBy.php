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

class GroupBy implements GroupByInterface
{
    /**
     * @var string
     */
    protected $column;

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    /**
     * {@inheritDoc}
     */
    public function getColumn() : string
    {
        return $this->column;
    }
}
