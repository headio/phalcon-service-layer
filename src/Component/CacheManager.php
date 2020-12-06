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

namespace Headio\Phalcon\ServiceLayer\Component;

use Phalcon\Di\Injectable;
use Phalcon\Helper\Json;
use function microtime;
use function is_null;
use function sha1;
use Closure;
use JSON_UNESCAPED_SLASHES;
use JSON_UNESCAPED_UNICODE;

/**
 * Cache management component for the query repository.
 */
class CacheManager extends Injectable implements CacheManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function appendCacheParameter(string $entityName, array $params) : array
    {
        if (isset($params['cache'])) {
            return $params;
        }

        $params['cache'] = $this->createCacheParameters($entityName, $params);

        return $params;
    }

    /**
     * {@inheritDoc}
     */
    public function createCacheParameters(string $entityName, array $params) : array
    {
        return [
            'lifetime' => (int) $this->config->cache->modelCache->lifetime,
            'key' => $this->createKey($entityName, $params)
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function createKey(string $entityName, array $params) : string
    {
        if (isset($params['di'])) {
            unset($params['di']);
        }

        $prefix = ['version' => $this->fetchPrefix($entityName)];
        $params = array_merge($prefix, $params);

        return $this->encodeKey($params);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key) : bool
    {
        return $this->modelsCache->delete($key);
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function fetch(string $key, Closure $callback)
    {
        $data = $this->modelsCache->get($key);

        // cache data
        if (!$data) {
            $data = $callback();
            $config = $this->config->cache->modelCache;
            $this->modelsCache->set($key, $data, (int) $config->lifetime);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        return $this->modelsCache->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key) : bool
    {
        return (bool) $this->modelsCache->has($key);
    }

    /**
     * {@inheritDoc}
     */
    public function expire(array $entities) : void
    {
        foreach ($entities as $entity) {
            if ($this->modelsCache->has($entity)) {
                $this->delete($entity);
                $config = $this->config->cache->modelCache;
                $this->modelsCache->set($entity, microtime(true), (int) $config->lifetime);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function store(string $key, $data, ?int $lifetime = null) : bool
    {
        if (is_null($lifetime)) {
            $lifetime = $this->config->cache->modelCache->lifetime;
        }

        return $this->modelsCache->set($key, $data, $lifetime);
    }

    /**
     * Encode the cache key.
     */
    private function encodeKey(array $params) : string
    {
        $json = Json::encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return sha1($json);
    }

    /**
     * Fetch or create a prefix for the cache key.
     *
     * @return mixed
     */
    private function fetchPrefix(string $entityName)
    {
        if (!$this->modelsCache->has($entityName)) {
            /** @var Phalcon\Config */
            $config = $this->config->cache->modelCache;
            $this->modelsCache->set($entityName, microtime(true), (int) $config->lifetime);
        }

        return $this->modelsCache->get($entityName);
    }
}
