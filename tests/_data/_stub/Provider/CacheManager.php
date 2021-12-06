<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Provider;

use Headio\Phalcon\ServiceLayer\Component\CacheManager as Service;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class CacheManager implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'cacheManager',
            function () {
                return new Service();
            }
        );
    }
}
