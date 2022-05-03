<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Repository\Traits;

use Headio\Phalcon\ServiceLayer\Listener\CacheListener;
use Stub\Domain\Repository\CacheableRole as Repository;
use Phalcon\Events\EventInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\ResultsetInterface;
use DateInterval;
use IntegrationTester;

class CacheableTraitCest
{
    public function canSetEventsManager(IntegrationTester $I)
    {
        $I->wantToTest('getting and setting the events manager');

        $eventsManager = new EventsManager();
        $repository = new Repository();
        $repository->setEventsManager(
            $eventsManager,
        );

        $I->assertInstanceOf(ManagerInterface::class, $repository->getEventsManager());        
    }

    public function canAppendCacheDeclarationToQueryCriteria(IntegrationTester $I)
    {
        $I->wantToTest('appending a cache constraint to the query criteria');

        $repository = (new Repository());
        $criteria = $repository
            ->createCriteria()
            ->like('label', 'A')
        ;
        $query = $criteria->createBuilder()->getQuery();
        $result = $repository->appendCache(
            $query,
            60,
        );

        $I->assertArrayHasKey('lifetime', $result->getCacheOptions());
        $I->assertArrayHasKey('key', $result->getCacheOptions());
    }

    public function canAppendCacheLifetime(IntegrationTester $I)
    {
        $I->wantToTest('appending a cache lifetime constraint');

        $repository = (new Repository())
            ->remember(60)
        ;

        $I->assertEquals(60, $repository->getLifetime());

        $repository = (new Repository())
            ->remember(new DateInterval("P5D"))
        ;

        $I->assertInstanceOf(DateInterval::class, $repository->getLifetime());
    }

    public function canFetchResultsetFromCacheOrStorage(IntegrationTester $I)
    {
        $I->wantToTest('fetching a collection of records from cache or storage');

        $repository = (new Repository());
        $eventsManager = new EventsManager();
        $eventsManager->enablePriorities(true);
        $repository->setEventsManager(
            $eventsManager,
        );
        $cacheManager = $I->getService('cacheManager');
        $eventsManager->attach(
            'cache',
            new CacheListener(
                $cacheManager
            ),
            100
        );
        $criteria = $repository
            ->createCriteria()
            ->like('label', 'A')
        ;
        $query = $criteria
            ->createBuilder()
            ->getQuery()
        ;
        $result = $repository->fromCache(
            $query,
            fn () => $query->execute(),
            new DateInterval("P5D")
        );

        $I->assertInstanceOf(ResultsetInterface::class, $result);
    }

    public function canFetchModelFromCacheOrStorage(IntegrationTester $I)
    {
        $I->wantToTest('fetching a model from cache or storage');

        $repository = (new Repository());
        $eventsManager = new EventsManager();
        $eventsManager->enablePriorities(true);
        $repository->setEventsManager(
            $eventsManager,
        );
        $cacheManager = $I->getService('cacheManager');
        $eventsManager->attach(
            'cache',
            new CacheListener(
                $cacheManager
            ),
            100
        );
        $criteria = $repository
            ->createCriteria()
            ->eq('label', 'Admin')
        ;
        $query = $criteria->createBuilder()->getQuery();
        $result = $repository->fromCache(
            $query,
            fn () => $query->execute(),
            new DateInterval("P1D")
        );

        $I->assertInstanceOf(ResultsetInterface::class, $result);
    }

    public function canToggleCacheable(IntegrationTester $I)
    {
        $I->wantToTest('switching the cache on and off');

        $repository = (new Repository())
            ->nocache()
        ;

        $I->assertFalse($repository->isCacheable());

        $repository->cacheable();

        $I->assertTrue($repository->isCacheable());
    }
}


