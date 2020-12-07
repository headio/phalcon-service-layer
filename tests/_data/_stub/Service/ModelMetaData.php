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

use Phalcon\Cache\AdapterFactory;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Model\MetaData\Memory;
use Phalcon\Mvc\Model\MetaData\Stream;
use Phalcon\Mvc\Model\MetaData\Strategy\Annotations as Strategy;
use Phalcon\Storage\SerializerFactory;
class ModelMetaData implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di) : void
    {
        $di->setShared(
            'modelsMetadata',
            function () {
                $config = $this->get('config');
                $serializerFactory = new SerializerFactory();
                $adapterFactory = new AdapterFactory($serializerFactory);

                if (!isset($config->metadata) || $config->debug) {
                    $service = new Memory();
                } else {
                    $adapter = 'Phalcon\\Mvc\\Model\\MetaData\\' . $config->metadata->adapter;
                    $options = $config->metadata->options->toArray();
                    $service = new $adapter(
                        $adapterFactory,
                        $options
                    );
                }

                $service->setStrategy(new Strategy());

                return $service;
            }
        );
    }
}
