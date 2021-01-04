<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Component;

use Stub\Domain\Entity\User;
use IntegrationTester;

class CacheManagerCest
{
    private $entity;

    public function _before(IntegrationTester $I)
    {
        $this->entity = new User();
        $this->di = $I->getApplication()->getDI();
    }

    public function createCacheKey(IntegrationTester $I)
    {
        $I->wantTo('Create a cache key via the cache manager');

        $entityName = get_class($this->entity);
        $service = $this->di->get('cacheManager');
        $result = $service->createKey($entityName, $this->_data()['criteria']);
        expect(is_string($result))->true();
        expect(strlen($result))->equals(40);
    }

    public function createCacheParameter(IntegrationTester $I)
    {
        $I->wantTo('Create cache parameters via the cache manager');

        $entityName = get_class($this->entity);
        $service = $this->di->get('cacheManager');
        $result = $service->createCacheParameters($entityName, $this->_data()['criteria']);

        expect($result)->hasKey('lifetime');
        expect($result)->hasKey('key');
    }

    public function appendCacheParameter(IntegrationTester $I)
    {
        $I->wantTo('Append a cache constraint to the query criteria');

        $entityName = get_class($this->entity);
        $service = $this->di->get('cacheManager');
        $result = $service->appendCacheParameter($entityName, $this->_data()['criteria']);

        expect($result)->hasKey('cache');
    }

    public function hasCacheValue(IntegrationTester $I)
    {
        $I->wantTo('Fetch cache key after storing data in the cache service');

        $service = $this->di->get('cacheManager');
        $entityName = get_class($this->entity);
        $key = $service->createKey($entityName, $this->_data()['criteria']);

        expect($service->store($key, $this->_data()['cacheVal'], 60))->true();

        expect($service->has($key))->true();
    }

    public function fetchCacheValue(IntegrationTester $I, $key)
    {
        $I->wantTo('Fetch cache data after storing data in the cache service');

        $service = $this->di->get('cacheManager');
        $entityName = get_class($this->entity);
        $key = $service->createKey($entityName, $this->_data()['criteria']);

        expect($service->store($key, $this->_data()['cacheVal'], 60))->true();

        expect($service->get($key))->equals($this->_data()['cacheVal']);
    }

    public function fetchAndStoreCacheValue(IntegrationTester $I, $key)
    {
        $I->wantTo('Fetch and cache data in the cache store in one call');

        $service = $this->di->get('cacheManager');
        $entityName = get_class($this->entity);
        $key = $service->createKey($entityName, $this->_data()['criteria']);
        $data = function () {
            return $this->_data()['cacheVal'];
        };
        $service->fetch($key, $data, 60);

        expect($service->get($key))->equals($this->_data()['cacheVal']);
    }

    public function deleteCacheValue(IntegrationTester $I, $key)
    {
        $I->wantTo('Delete the cached data via the cache manager');

        $service = $this->di->get('cacheManager');
        $entityName = get_class($this->entity);
        $key = $service->createKey($entityName, $this->_data()['criteria']);

        expect($service->store($key, $this->_data()['cacheVal'], 60))->true();

        expect($service->get($key))->equals($this->_data()['cacheVal']);

        $service->delete($key);

        expect($service->get($key))->null();
    }

    public function ExpireCacheKey(IntegrationTester $I, $key)
    {
        $I->wantTo('Expire the cache key via the cache manager');

        $service = $this->di->get('cacheManager');
        $entityName = get_class($this->entity);
        $key = $service->createKey($entityName, $this->_data()['criteria']);
        $data = function () {
            return $this->_data()['cacheVal'];
        };

        // cached
        $service->fetch($key, $data, 60);

        expect($service->get($key))->equals($data());

        // Increments the cache key prefix for the entity;
        // does not delete the cached data.
        $service->expire(
            [
                $entityName
            ]
        );

        // expired on fetch
        expect($service->get($this->_data()['versionPrefix']))->notSame($data());
    }

    /**
     * Return test data
     */
    protected function _data(): array
    {
        return [
            'criteria' => ['label = :label:', 'order' => 'label', 'limit' => 10],
            'cacheVal' => 'Hello',
            'versionPrefix' => 'StubDomainEntityUser'
        ];
    }
}
