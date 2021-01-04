<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Entity\Behavior;

use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\BehaviorInterface;
use Phalcon\Mvc\ModelInterface;

class CacheInvalidateable extends Behavior implements BehaviorInterface
{
    /**
     * {@inheritDoc}
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     */
    public function notify($eventType, ModelInterface $model): void
    {
        $items = $this->getOptions($eventType)['invalidate'] ?? $this->getOptions()['invalidate'] ?? [];

        if ($eventType === 'afterDelete' || $eventType === 'afterSave') {
            $model->getDI()->get('cacheManager')->expire($items);
        }
    }
}
