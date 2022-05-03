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
use DateTimeImmutable;

class Publishable extends Behavior implements BehaviorInterface
{
    /**
     * {@inheritDoc}
     */
    public function notify(string $type, ModelInterface $model): void
    {
        $dt = new DateTimeImmutable('now');
        $expiry = $this->getOptions()['expiry'] ?? '+10 years';

        if ($type === 'beforeSave') {
            if ($model->getPublished()) {
                if (!$model->getPublishFrom() instanceof DateTimeImmutable) {
                    $model->setPublishFrom($dt);
                }

                if (!$model->getPublishTo() instanceof DateTimeImmutable) {
                    $model->setPublishTo($dt->modify($expiry));
                }
            } else {
                $model->setPublishFrom(null);
                $model->setPublishTo(null);
            }
        }
    }
}
