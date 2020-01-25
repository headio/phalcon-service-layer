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

namespace Headio\Phalcon\DomainLayer\Filter;

use function is_null;
use function strcmp;
use function strtoupper;

class OrderBy implements OrderByInterface
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var string|null
     */
    protected $direction;

    public function __construct(string $column, ?string $direction = null)
    {
        $this->column = $column;
        $this->direction = $direction;

        if (!is_null($this->direction)) {
            if (0 === strcmp($this->direction, OrderByInterface::ASC)) {
                $this->direction = null;
            } else {
                $this->direction = OrderByInterface::DESC;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getColumn() : string
    {
        return $this->column;
    }

    /**
     * {@inheritDoc}
     */
    public function getDirection() : ?string
    {
        return $this->direction;
    }

    /**
     * {@inheritDoc}
     */
    public function hasDirection() : bool
    {
        return !empty($this->direction);
    }
}
