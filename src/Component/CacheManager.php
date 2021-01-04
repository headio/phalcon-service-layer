<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Component;

use Phalcon\Di\Injectable;
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
    public function appendCacheParameter(string $entityName, array $params): array
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
    public function createCacheParameters(string $entityName, array $params): array
    {
        return [
            'lifetime' => (int) $this->config->cache->modelCache->lifetime,
            'key' => $this->createKey($entityName, $params)
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function createKey(string $entityName, array $params): string
    {
        $key = ['version' => $this->fetchPrefix($entityName)];

        if (isset($params['di'])) {
            unset($params['di']);
        }

        $params = array_merge($key, $params);

        return $this->encodeKey($params);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key): bool
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
            $this->modelsCache->save($key, $data, (int) $config->lifetime);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key): ?string
    {
        return $this->modelsCache->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        return (bool) $this->modelsCache->exists($key);
    }

    /**
     * {@inheritDoc}
     */
    public function expire(array $entities): void
    {
        foreach ($entities as $entity) {
            if ($this->modelsCache->exists($entity)) {
                $this->delete($entity);
                $config = $this->config->cache->modelCache;
                $this->modelsCache->save($entity, microtime(true), (int) $config->lifetime);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function store(string $key, $data, ?int $lifetime = null): void
    {
        if (is_null($lifetime)) {
            $lifetime = $this->config->cache->modelCache->lifetime;
        }

        $this->modelsCache->save($key, $data, $lifetime);
    }

    /**
     * Encode the cache key.
     */
    private function encodeKey(array $params): string
    {
        $json = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return sha1($json);
    }

    /**
     * Fetch or create a prefix for the cache key.
     *
     * @return string|float
     */
    private function fetchPrefix(string $entityName)
    {
        if (!$this->modelsCache->exists($entityName)) {
            /** @var Phalcon\Config */
            $config = $this->config->cache->modelCache;
            $this->modelsCache->save($entityName, microtime(true), (int) $config->lifetime);
        }

        return $this->modelsCache->get($entityName);
    }
}
