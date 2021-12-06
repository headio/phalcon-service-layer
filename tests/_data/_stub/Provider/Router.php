<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Provider;

use Headio\Phalcon\ServiceLayer\Exception\OutOfRangeException;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;
use Phalcon\Cli\Router as CliRouter;
use Phalcon\Mvc\Router\Annotations as Service;

class Router implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'router',
            function () use ($di) {
                $config = $di->get('config');

                if ($config->cli) {
                    $service = new CliRouter();

                    if (!empty($module = $config->dispatcher->path('defaultModule', null))) {
                        $service->setDefaultModule($module);
                    }

                    return $service;
                }

                if (!isset($config->modules)) {
                    throw new OutOfRangeException('Undefined modules');
                }

                if (!isset($config->routes)) {
                    throw new OutOfRangeException('Undefined routes');
                }

                $service = new Service(false);
                $service->removeExtraSlashes(true);
                $service->setControllerSuffix('');
                $service->setDefaultAction($config->dispatcher->defaultAction);
                $service->setDefaultController($config->dispatcher->defaultController);
                $service->setDefaultNamespace($config->dispatcher->defaultControllerNamespace);
                $service->setDefaultModule($config->dispatcher->defaultModule);

                foreach ($config->modules->toArray() ?? [] as $module => $settings) {
                    if (!$config->routes->get($module, false)) {
                        continue;
                    }
                    foreach ($config->routes->{$module}->toArray() ?? [] as $key => $val) {
                        $service->addModuleResource($module, $key, $val);
                    }
                }

                return $service;
            }
        );
    }
}
