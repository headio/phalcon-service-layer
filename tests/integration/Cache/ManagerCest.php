<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Cache;

use Stub\Domain\Repository\User as Repository;
use Stub\Domain\Model\User as Model;
use Phalcon\Di\DiInterface;
use function ctype_xdigit;
use IntegrationTester;

class ManagerCest
{
    public function canGenerateCacheKeyUsingArray(IntegrationTester $I)
    {
        $I->wantToTest('generating a cache key using an array of query parameters');

        $service = $I->getService('cacheManager');
        $modelName = Model::class;
        $result = $service->generateKey(
            $modelName,
            [
                'condition' => 'label = :label:',
                'order' => 'label',
                'limit' => 10,
            ],
        );
        $I->assertTrue(ctype_xdigit($result));
    }

    public function canGenerateCacheKeyUsingQueryInterface(IntegrationTester $I)
    {
        $I->wantToTest('generating a cache key using a query instance');

        $service = $I->getService('cacheManager');
        $repository = (new Repository());
        $criteria = $repository
            ->createCriteria()
            ->like('email', 'headio')
        ;
        $modelName = $repository->getModel();
        $query = $criteria->createBuilder()->getQuery();
        $result = $service->generateKey(
            $modelName,
            $query,
        );

        $I->assertTrue(ctype_xdigit($result));
    }

    public function canAppendCacheDeclarationToQuery(IntegrationTester $I)
    {
        $I->wantToTest('appending a cache constraint to the query criteria');

        $service = $I->getService('cacheManager');
        $repository = (new Repository());
        $criteria = $repository
            ->createCriteria()
            ->like('email', 'headio')
        ;
        $query = $criteria->createBuilder()->getQuery();
        $modelName = $repository->getModel();
        $result = $service->appendCache(
            $modelName,
            $query,
            60,
        );

        $I->assertArrayHasKey('lifetime', $result->getCacheOptions());
        $I->assertArrayHasKey('key', $result->getCacheOptions());
    }

    public function canFetchFromCacheUsingArrayAsKeyParameter(IntegrationTester $I)
    {
        $I->wantToTest('fetching data from cache using an array as key generation parameter');

        $service = $I->getService('cacheManager');
        $key = $service->generateKey(
            Model::class,
            ['name = :name:', 'order' => 'name', 'limit' => 10],
        );
        $result = $service->fromCache(
            $key,
            fn () => 'Hello array',
            60,
        );

        $I->assertTrue(
            $service->getCache()->has($key)
        );

        $I->assertEquals('Hello array', $result);

        // delete the cache key
        $I->assertTrue(
            $service->getCache()->delete($key)
        );
    }

    public function canFetchFromCacheUsingQueryAsKeyParameter(IntegrationTester $I)
    {
        $I->wantToTest('fetching data from cache using query instance as key generation parameter');

        $service = $I->getService('cacheManager');
        $repository = (new Repository());
        $criteria = $repository
            ->createCriteria()
            ->like('email', 'headio')
        ;
        $modelName = $repository->getModel();
        $key = $service->generateKey(
            $modelName,
            $criteria->createBuilder()->getQuery(),
        );
        $result = $service->fromCache(
            $key,
            fn () => 'Hello query',
            60,
        );

        $I->assertTrue(
            $service->getCache()->has($key)
        );

        $I->assertEquals('Hello query', $result);

        // delete the cache key
        $I->assertTrue(
            $service->getCache()->delete($key)
        );
    }

    public function invalidateCacheKeysForModel(IntegrationTester $I)
    {
        $I->wantToTest('evicting all the cache keys for a given model name');

        $service = $I->getService('cacheManager');
        $modelName = Model::class;
        $key = $service->generateKey(
            $modelName,
            ['name = :name:', 'order' => 'name', 'limit' => 20],
        );
        $result = $service->fromCache(
            $key,
            fn () => 'Hello data',  
            60,
        );

        $I->assertTrue(
            $service->getCache()->has($key)
        );

        $service->invalidateCache(
            [
                $modelName
            ]
        );

        // validate the incrementation has taken place by
        // hitting the cache again with the same parameters.
        $key2 = $service->generateKey(
            $modelName,
            ['name = :name:', 'order' => 'name', 'limit' => 20],
        );

        $I->assertNotEquals($key, $key2);

        // delete the cache key
        $I->assertTrue(
            $service->getCache()->delete($key)
        );
    }
}
