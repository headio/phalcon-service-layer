<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Listener;

use Headio\Phalcon\ServiceLayer\Cache\ManagerInterface;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Headio\Phalcon\ServiceLayer\Repository\RepositoryInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Events\EventInterface;

/**
 * This event listener provides caching functionality for repositories.
 */
class CacheListener
{
    public function __construct(private ManagerInterface $manager)
    {
    }

    /**
     * Appends a cache declaration to a Phalcon query instance.
     */
    public function append(
        EventInterface $event,
        RepositoryInterface $repository,
        QueryInterface $query,
    ): QueryInterface {
        // omit caching for this query
        if (!$repository->isCacheable()) {
            $event->stop();
            // reset the cacheable constraint declaration
            $repository->cacheable();

            return $query;
        }

        $model = $repository->getModel();
        $lifetime = $repository->getLifetime();
        $query = $this->manager->appendCache($model, $query, $lifetime);

        // reset the lifetime constraint declaration
        $repository->remember(null);

        return $query;
    }

    /**
     * Fetches data from cache or storage using the cache-aside
     * strategy.
     */
    public function fetch(
        EventInterface $event,
        RepositoryInterface $repository,
        array $context,
    ): ModelInterface|ResultsetInterface {
        [$key, $callable] = $context;

        // omit caching for this query
        if (!$repository->isCacheable()) {
            $event->stop();
            // reset the cacheable constraint declaration
            $repository->cacheable();
            $data = $callable();

            return $data;
        }

        $lifetime = $repository->getLifetime();

        // reset the lifetime constraint declaration
        $repository->remember(null);

        return $this->manager->fromCache($key, $callable, $lifetime);
    }
}
