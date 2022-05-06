<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Repository\Traits;

use Headio\Phalcon\ServiceLayer\Exception\NotFoundException;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Headio\Phalcon\ServiceLayer\Repository\RepositoryInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Row;
use Closure;
use DateInterval;
use function current;

/**
 * @property \Headio\Phalcon\ServiceLayer\Cache\Manager $cacheManager
 */
trait CacheableTrait
{
    protected bool $cacheable = true;

    private ManagerInterface|null $eventsManager = null;

    protected DateInterval|int|null $lifetime = null;

    /**
     * Append a cache constraint to a Phalcon criteria or query instance.
     */
    public function appendCache(
        CriteriaInterface|QueryInterface $criteria,
        DateInterval|int $lifetime = null,
    ): CriteriaInterface|QueryInterface {
        $criteria = $this->cacheManager->appendCache(
            $this->getModel(),
            $criteria,
            $lifetime,
        );

        return $criteria;
    }

    /**
     * Reset the cacheable constraint flag.
     */
    public function cacheable(): void
    {
        $this->cacheable = true;
    }


    /**
     * Fetch a column value by query criteria from cache or storage.
     */
    public function fetchColumn(CriteriaInterface $criteria): mixed
    {
        $query = $criteria
            ->createBuilder()
            ->getQuery()
        ;
        $this->eventsManager->fire('cache:append', $this, $query);
        $resultset = $query->getSingleResult();

        if ($resultset instanceof Row) {
            return current($resultset->toArray());
        }

        return null;
    }

    /**
     * Fetch records by query criteria from cache or storage.
     */
    public function find(CriteriaInterface $criteria): ResultsetInterface
    {
        $query = $criteria
            ->createBuilder()
            ->getQuery()
        ;
        $this->eventsManager->fire('cache:append', $this, $query);

        return $query->execute();
    }

    /**
     * Fetch first record by query criteria from cache or storage.
     *
     * @throws NotFoundException
     */
    public function findFirst(CriteriaInterface $criteria): ModelInterface
    {
        $query = $criteria
            ->createBuilder()
            ->getQuery()
            ->setUniqueRow(true)
        ;
        $this->eventsManager->fire('cache:append', $this, $query);
        $model = $query->execute();

        if (!$model instanceof ModelInterface) {
            throw new NotFoundException('404 Not Found');
        }

        return $model;
    }

    /**
     * Fetch data from cache or storage.
     */
    public function fromCache(
        QueryInterface|array $query,
        Closure $callable,
        DateInterval|int $lifetime = null,
    ): ResultsetInterface|ModelInterface|null {
        $key = $this->cacheManager->generateKey(
            $this->getModel(),
            $query,
        );

        return $this->eventsManager->fire(
            'cache:fetch',
            $this,
            [$key, $callable],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getEventsManager(): ManagerInterface|null
    {
        return $this->eventsManager;
    }

    /**
     * Return the cache lifetime constraint.
     */
    public function getLifetime(): DateInterval|int|null
    {
        return $this->lifetime;
    }

    /**
     * Return the related models from cache or storage.
     */
    protected function getRelated(
        string $alias,
        ModelInterface $model,
        CriteriaInterface $criteria = null,
        string $method = null,
    ): ResultsetInterface|ModelInterface|bool|int {
        $keyParams = [
            'id' => $model->getId(),
            'relation' => $alias,
        ];

        return $this->fromCache(
            $keyParams,
            fn () => parent::getRelated($alias, $model, $criteria, $method),
            $this->getLifetime(),
        );
    }

    /**
     * Determine whether the query should be cached.
     */
    public function isCacheable(): bool
    {
        return $this->cacheable;
    }

    /**
     * Omit the cache for the current query.
     */
    public function nocache(): RepositoryInterface
    {
        $this->cacheable = false;

        return $this;
    }

    /**
     * Append a cache lifetime constraint to the current query.
     */
    public function remember(
        DateInterval|int|null $lifetime,
    ): RepositoryInterface {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setEventsManager(ManagerInterface $eventsManager): void
    {
        $this->eventsManager = $eventsManager;
    }
}
