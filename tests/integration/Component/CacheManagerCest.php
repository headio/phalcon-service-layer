<?php
/*
 * This source file is subject to the MIT License.
 *
 * (c) Dominic Beck <dominic@headcrumbs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this package.
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
        $I->wantTo('Create a cache key using the cache manager');

        $entityName = get_class($this->entity);
        $service = $this->di->get('cacheManager');
        $result = $service->createKey($entityName, $this->_data()['criteria']);

        expect(is_string($result))->true();
        expect(strlen($result))->equals(40);
    }

    public function createCacheParameter(IntegrationTester $I)
    {
        $I->wantTo('Create cache parameters using the cache manager');

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

    public function storeCacheValue(IntegrationTester $I)
    {
        $I->wantTo('Store a data in the cache service via the cache manager');

        $service = $this->di->get('cacheManager');
        $entityName = get_class($this->entity);
        $key = $service->createKey($entityName, $this->_data()['criteria']);
        $service->store($key, $this->_data()['cacheVal'], 60);

        expect($service->has($key))->true();
    }

    public function fetchCacheValue(IntegrationTester $I, $key)
    {
        $I->wantTo('Fetch data from the cache store via the cache manager');

        $service = $this->di->get('cacheManager');
        $entityName = get_class($this->entity);
        $key = $service->createKey($entityName, $this->_data()['criteria']);
        $service->store($key, $this->_data()['cacheVal'], 60);

        expect($service->get($key))->equals($this->_data()['cacheVal']);
    }

    /**
     * Return test data
     */
    protected function _data() : array
    {
        return [
            'criteria' => ['label = :label:', 'order' => 'label', 'limit' => 10],
            'cacheVal' => 'Hello',
        ];
    }
}
