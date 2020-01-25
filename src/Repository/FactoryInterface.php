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

namespace Headio\Phalcon\DomainLayer\Repository;

interface FactoryInterface
{
    /**
     * Instantiate a new repository
     */
    public static function create(string $class, bool $cache) : RepositoryInterface;
}
