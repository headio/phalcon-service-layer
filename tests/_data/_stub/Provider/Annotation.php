<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Provider;

use Phalcon\Annotations\Adapter\Memory;
use Phalcon\Annotations\AnnotationsFactory;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class Annotation implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'annotations',
            function () use ($di) {
                $config = $di->get('config');

                if ($config->debug) {
                    $service = new Memory();
                } else {
                    $factory = new AdapterFactory();
                    $service = $factory->load($config->annotations);
                }

                return $service;
            }
        );
    }
}
