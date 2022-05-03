<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Event;

use Headio\Phalcon\ServiceLayer\Listener\CacheListener;
use Stub\Domain\Repository\CacheableUser as Repository;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Di\DiInterface;
use IntegrationTester;

class CacheCest
{
    private EventsManager $eventsManager;

    public function _before(IntegrationTester $I)
    {
        $eventsManager = new EventsManager();
        $service = $I->getService('cacheManager');
        $repository = new Repository();
        $repository->setEventsManager($eventsManager);
        $model = $repository->getModel();
        $eventsManager->attach(
            'cache',
            new CacheListener(
                $service
            )
        );
        $this->eventsManager = $eventsManager;
    }

    public function hasRegisteredListener(IntegrationTester $I)
    {
        $listeners = $this->eventsManager->hasListeners(
            'cache'
        );

        $I->assertTrue($listeners);
    }
}
