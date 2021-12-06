<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Provider;

use Stub\Middleware\Paginator;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;
use Phalcon\Cli\Dispatcher as CliService;
use Phalcon\Mvc\Dispatcher as MvcService;

class Dispatcher implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'dispatcher',
            function () {
                $config = $this->get('config');
                $eventsManager = $this->get('eventsManager');

                if ($config->cli) {
                    $service = new CliService();
                    $service->setTaskSuffix('');

                    if (!empty($namespace = $config->dispatcher->path('defaultTaskNamespace', null))) {
                        $service->setDefaultNamespace($namespace);
                    }

                    $service->setEventsManager($eventsManager);

                    return $service;
                }

                $service = new MvcService();
                $service->setControllerSuffix('');
                $service->setDefaultNamespace($config->dispatcher->defaultControllerNamespace);
                $service->setNamespaceName($config->dispatcher->defaultControllerNamespace);
                $service->setEventsManager($eventsManager);
                $service->getEventsManager()->attach(
                    'dispatch',
                    new Paginator()
                );

                return $service;
            }
        );
    }
}
