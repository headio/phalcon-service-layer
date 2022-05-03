<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Provider;

use Stub\View\VoltExtension;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;
use Phalcon\Events\EventInterface;
use Phalcon\Mvc\DispatcherInterface;
use Phalcon\Mvc\View as Service;
use Phalcon\Mvc\View\Engine\Volt;

class View implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'view',
            function () use ($di) {
                $config = $this->get('config');
                $service = new Service();
                $volt = new Volt($service, $di);
                $volt->setOptions(
                    [
                        'always' => $config->debug,
                        'path' => $config->view->compiledPath,
                        'separator' => $config->view->compiledSeparator,
                    ]
                );
                $compiler = $volt->getCompiler();
                $compiler->addExtension(new VoltExtension());
                $service->registerEngines(
                    [
                        '.volt' => $volt,
                        '.phtml' => 'Phalcon\\Mvc\\View\\Engine\\Php'
                    ]
                );

                if (isset($config->view->defaultPath)) {
                    $service->setViewsDir($config->view->get('defaultPath'));
                    $service->setEventsManager($di->get('eventsManager'));
                    $dispatcher = $di->get('dispatcher');
                    /**
                     * Attach dispatcher events from the view context
                     */
                    $dispatcher->getEventsManager()->attach('dispatch', new self());
                } else {
                    $service->disable();
                }

                return $service;
            }
        );
    }
}
