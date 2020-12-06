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

namespace Stub\Service;

use Phalcon\Storage\SerializerFactory;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class ModelCache implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di) : void
    {
        $di->set(
            'modelsCache',
            function () {
                $config = $this->get('config');
                $serializerFactory = new SerializerFactory();
                $adapter = 'Phalcon\\Cache\\Adapter\\' . $config->cache->modelCache->adapter;
                $options = $config->cache->modelCache->options->toArray();
                $service = new $adapter($serializerFactory, $options);

                return $service;
            }
        );
    }
}
