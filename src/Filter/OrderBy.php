<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Filter;

use function is_null;
use function strcasecmp;
use function strtoupper;

class OrderBy implements OrderByInterface
{
    public function __construct(
        private string $column,
        private string|null $direction = null,
    ) {
        if (!is_null($this->direction)) {
            if (0 === strcasecmp($this->direction, OrderByInterface::ASC)) {
                $this->direction = null;
            } else {
                $this->direction = OrderByInterface::DESC;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * {@inheritDoc}
     */
    public function getDirection(): ?string
    {
        return $this->direction;
    }

    /**
     * {@inheritDoc}
     */
    public function hasDirection(): bool
    {
        return !empty($this->direction);
    }
}
