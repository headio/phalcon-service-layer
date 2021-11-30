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
use Phalcon\Helper\Json;
use Closure;
use JSON_UNESCAPED_SLASHES;
use JSON_UNESCAPED_UNICODE;
use function microtime;
use function sha1;

/**
 * Cache management component for the query repository.
 *
 * @property \Phalcon\Config $config
 * @property \Psr\SimpleCache\CacheInterface $modelsCache
 */
class CacheManager extends Injectable implements CacheManagerInterface
{
    /**
     * {@inheritDoc}
     *
     * @param array<mixed> $params
     * @return array<mixed>
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
     *
     * @param array<mixed> $params
     * @return array<string,mixed>
     */
    public function createCacheParameters(string $entityName, array $params): array
    {
        /** @var \Phalcon\Config $config */
        $config = $this->config->cache->modelCache;

        return [
            'lifetime' => (int) $config->options->lifetime,
            'key' => $this->createKey($entityName, $params)
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function createKey(string $entityName, array $params): string
    {
        if (isset($params['di'])) {
            unset($params['di']);
        }

        $prefix = ['version' => $this->fetchVersion($entityName)];
        $params = array_merge($prefix, $params);

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
            $this->modelsCache->set($key, $data, (int) $config->options->lifetime);
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
    public function has(string $key): bool
    {
        return $this->modelsCache->has($key);
    }

    /**
     * {@inheritDoc}
     */
    public function expire(array $collection): void
    {
        foreach ($collection as $item) {
            /** @var string */
            $key = $this->normalizeKey($item);

            if ($this->modelsCache->has($key)) {
                /** @var bool */
                $result = $this->delete($key);
                $config = $this->config->cache->modelCache;
                /** @var bool */
                $result = $this->store(
                    $key,
                    microtime(true),
                    (int) $config->options->lifetime
                );
                unset($key);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function store(string $key, $data, int $lifetime): bool
    {
        return $this->modelsCache->set($key, $data, $lifetime);
    }

    /**
     * Encode the cache key.
     */
    private function encodeKey(array $params): string
    {
        $json = Json::encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return sha1($json);
    }

    /**
     * Fetch or create a cache key version prefix for the entity.
     *
     * @return mixed
     */
    private function fetchVersion(string $entityName)
    {
        /** @var string */
        $key = $this->normalizeKey($entityName);

        if (!$this->modelsCache->has($key)) {
            /** @var \Phalcon\Config */
            $config = $this->config->cache->modelCache;
            $this->store(
                $key,
                microtime(true),
                (int) $config->options->lifetime
            );
        }

        return $this->modelsCache->get($key);
    }

    /**
     * Normalize the key version prefix for the entity
     * to satisify Phalcon's cache implementation.
     */
    private function normalizeKey(string $key): string
    {
        if (false !== strpos($key, '\\')) {
            $key = preg_replace('/[^a-zA-Z0-9=\s—–-]+/u', '', $key);
        }

        return $key;
    }
}
