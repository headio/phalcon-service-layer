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

use Closure;

interface CacheManagerInterface
{
    /**
     * Append cache parameter to the existing query criteria.
     */
    public function appendCacheParameter(string $entityName, array $params) : array;

    /**
     * Create the cache parameters.
     */
    public function createCacheParameters(string $entityName, array $params) : array;

    /**
     * Create a cache key based on the entity name and query parameters.
     */
    public function createKey(string $entityName, array $params) : string;

    /**
     * Deletes a value from the cache by its key.
     */
    public function delete(string $key) : bool;

    /**
     * Fetch data from cache or query storage (and cache).
     *
     * @return mixed
     */
    public function fetch(string $key, Closure $callback);

    /**
     * Get data from the cache store.
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * Does data in the cache store exist for the key.
     */
    public function has(string $key) : bool;

    /**
     * Expire all cache keys for entities.
     *
     * All cache keys are generated based on the query parameters
     * and prefixed with a microtime (stored in the cache
     * against a key representing the entity class name), see
     * fetchPrefix() for implementation.
     */
    public function expire(array $entities) : void;

    /**
     * Store data in the cache store.
     */
    public function store(string $key, $data, ?int $lifetime = null) : bool;
}
