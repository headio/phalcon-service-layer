<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Cache;

use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Phalcon\Config\ConfigInterface;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Support\Helper\Json\Encode as Json;
use Phalcon\Cache\CacheInterface;
use Closure;
use DateInterval;
use JSON_UNESCAPED_SLASHES;
use JSON_UNESCAPED_UNICODE;

use function array_merge;
use function microtime;
use function preg_replace;
use function sha1;
use function str_contains;

/**
 * Cache management component for the service layer.
 */
class Manager implements ManagerInterface
{
    public function __construct(
        private ConfigInterface $config,
        private CacheInterface $cache,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function appendCache(
        string $model,
        QueryInterface $query,
        DateInterval|int $lifetime = null,
    ): QueryInterface {
        $lifetime ??= $this->config->lifetime;
        $key = $this->generateKey($model, $query);
        $definition = [
            'lifetime' => $lifetime,
            'key' => $key,
        ];

        return $query->cache($definition);
    }

    /**
     * {@inheritDoc}
     */
    public function invalidateCache(array $collection): void
    {
        foreach ($collection as $item) {
            $key = $this->normalizeKey($item);

            if ($this->cache->has($key)) {
                $this->cache->getAdapter()->setForever(
                    $key,
                    microtime(true),
                );
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function fromCache(
        string $key,
        Closure $callback,
        DateInterval|int $lifetime = null,
    ): mixed {
        $data = $this->cache->get($key);

        if (!$data) {
            $data = $callback();
            $result = $this->cache->set($key, $data, $lifetime);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function generateKey(
        string $model,
        QueryInterface|array $criteria,
    ): string {
        if ($criteria instanceof QueryInterface) {
            $criteria = $criteria->getSql();
        }

        $version = $this->fetchVersion($model);
        $params = ['v' => $version] + $criteria;
        $hash = $this->hashKey($params);

        return $hash;
    }

    /**
     * {@inheritDoc}
     */
    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    /**
     * Fetch or create a unique version identifier for
     * a model class name.
     */
    private function fetchVersion(string $model): float
    {
        $key = $this->normalizeKey($model);

        if (!$this->cache->has($key)) {
            $this->cache->getAdapter()->setForever(
                $key,
                microtime(true),
            );
        }

        $version = (float) $this->cache->get($key);

        return $version;
    }

    /**
     * Return the cache key as a sha1 hash string representation.
     */
    private function hashKey(array $params): string
    {
        $json = (new Json())($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return sha1($json);
    }

    /**
     * Normalize the key to satisfy Phalcon's cache implementation.
     */
    private function normalizeKey(string $model): string
    {
        if (str_contains($model, '\\')) {
            $key = preg_replace('/[^a-zA-Z0-9-_.=\s—–-]+/u', '', $model);
        }

        return $key;
    }
}
