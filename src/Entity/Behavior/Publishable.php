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
use DateTime;

class Publishable extends Behavior implements BehaviorInterface
{
    /**
     * {@inheritDoc}
     */
    public function notify($eventType, ModelInterface $model): void
    {
        $dt = new DateTime('now');
        $expiry = '+10 years';

        if ($eventType === 'beforeSave') {
            if (isset($this->getOptions()['expiry'])) {
                $expiry = $this->getOptions()['expiry'];
            }
            if ($model->getPublished()) {
                if (!$model->getPublishFrom() instanceof DateTime) {
                    $model->setPublishFrom($dt);
                }

                if (!$model->getPublishTo() instanceof DateTime) {
                    $model->setPublishTo($dt->modify($expiry));
                }
            } else {
                $model->setPublishFrom(null);
                $model->setPublishTo(null);
            }
        }
    }
}
