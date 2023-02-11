<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Cache;

use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Cache\CacheInterface;
use Closure;
use DateInterval;

interface ManagerInterface
{
    /**
     * Append a cache constraint to a Phalcon query instance.
     */
    public function appendCache(
        string $model,
        QueryInterface $query,
        DateInterval|int $lifetime = null,
    ): QueryInterface;

    /**
     * Invalidate cache keys for a model or a collection of models.
     *
     * All cache keys are created based on the query parameters and
     * contain a unique identifier stored in the cache against a key
     * representing the model's class name.
     *
     * When a model is created, updated or removed, the unique identifier
     * changes and thus the data is written to the new key; the old key
     * will never be requested again and whilst this generates a lot of
     * cache garbage, cache eviction is best handled by the underlying
     * cache adapter by means of a cache eviction policy.
     *
     * @var array<int,string>
     */
    public function invalidateCache(array $collection): void;

    /**
     * Fetch the data from cache or storage; this implementation
     * follows the cache-aside approach.
     */
    public function fromCache(
        string $key,
        Closure $callback,
        DateInterval|int $lifetime = null,
    ): mixed;

    /**
     * Generate a unique cache key using the query criteria.
     */
    public function generateKey(
        string $model,
        QueryInterface|array $criteria,
    ): string;

    /**
     * Return an instance of the cache component.
     */
    public function getCache(): CacheInterface;
}
