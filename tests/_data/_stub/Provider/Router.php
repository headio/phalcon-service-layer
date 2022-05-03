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
            function () {
                $config = $this->get('config');

                if ($config->get('cli', false)) {
                    $service = new CliRouter();

                    if (!empty($module = $config->dispatcher->path('defaultModule', null))) {
                        $service->setDefaultModule($module);
                    }

                    return $service;
                }

                if (!$config->has('modules')) {
                    throw new OutOfRangeException('Undefined modules');
                }

                if (!$config->has('routes')) {
                    throw new OutOfRangeException('Undefined routes');
                }

                $service = new Service(false);
                $service->removeExtraSlashes(true);
                $service->setControllerSuffix('');
                $service->setDefaultAction($config->dispatcher->defaultAction);
                $service->setDefaultController($config->dispatcher->defaultController);
                $service->setDefaultNamespace($config->dispatcher->defaultControllerNamespace);
                $service->setDefaultModule($config->dispatcher->defaultModule);

                foreach ($config->get('modules')?->toArray() ?? [] as $module => $settings) {
                    if (!$config->get('routes')->has($module)) {
                        continue;
                    }
                    foreach ($config->get('routes')->{$module}?->toArray() ?? [] as $key => $val) {
                        $service->addModuleResource($module, $key, $val);
                    }
                }

                return $service;
            }
        );
    }
}
