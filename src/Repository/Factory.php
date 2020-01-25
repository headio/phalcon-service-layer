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

use Headio\Phalcon\DomainLayer\Exception\InvalidArgumentException;
use Headio\Phalcon\DomainLayer\Exception\RuntimeException;
use function class_exists;
use function sprintf;
use ReflectionClass;

/**
 * A static factory providing repository instantiation.
 */
class Factory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException|RuntimeException
     */
    public static function create(string $class, bool $cache) : RepositoryInterface
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(
                sprintf('Repository class %s does not exist.', $class)
            );
        }

        $rc = new ReflectionClass($class);

        if (!$rc->implementsInterface(__NAMESPACE__ . '\\RepositoryInterface')) {
            throw new RuntimeException(
                sprintf('Repository %s does not implement repository interface.', $rc->getShortName())
            );
        }

        return new $class($cache);
    }
}
