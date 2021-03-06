<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Filter;

interface GroupByInterface
{
    /**
     * Return the group by column constraint
     */
    public function getColumn(): string;
}
