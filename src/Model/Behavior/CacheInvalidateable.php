<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Model\Behavior;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\BehaviorInterface;

class CacheInvalidateable extends Behavior implements BehaviorInterface
{
    /**
     * {@inheritDoc}
     */
    public function notify(string $type, ModelInterface $model): void
    {
        $collection = $this->getOptions($type)['invalidate'] ?? $this->getOptions()['invalidate'] ?? [];

        // evict all cached data for a model or collection of models
        if ($type === 'afterDelete' || $type === 'afterSave') {
            $model->getDI()->get('cacheManager')->invalidateCache($collection);
        }
    }
}
