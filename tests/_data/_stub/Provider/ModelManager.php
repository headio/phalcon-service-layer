<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Provider;

use Stub\Middleware\EntityMapper;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Model\Manager as Service;

class ModelManager implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'modelsManager',
            function () use ($di) {
                $service = new Service();
                $eventsManager = $di->get('eventsManager');
                $service->setEventsManager($eventsManager);
                $eventsManager->attach('modelsManager', new EntityMapper());

                return $service;
            }
        );
    }
}
