<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Provider;

use Phalcon\Cache\CacheFactory;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class ModelCache implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di): void
    {
        $di->set(
            'modelsCache',
            function () use ($di) {
                $config = $di->get('config')->cache->modelCache;
                $serializerFactory = new SerializerFactory();
                $adapterFactory = new AdapterFactory($serializerFactory);
                $cacheFactory = new CacheFactory($adapterFactory);
                /** @var \Psr\SimpleCache\CacheInterface */
                $service = $cacheFactory->load($config);

                return $service;
            }
        );
    }
}
