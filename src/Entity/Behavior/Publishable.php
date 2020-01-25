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

namespace Headio\Phalcon\DomainLayer\Entity\Behavior;

use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\BehaviorInterface;
use Phalcon\Mvc\ModelInterface;
use DateTime;

class Publishable extends Behavior implements BehaviorInterface
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
    public function notify($eventType, ModelInterface $model)
    {
        $dt = new DateTime('now');

        if ($eventType === 'beforeSave') {
            if ($model->getPublished()) {
                if (!$model->getPublishFrom() instanceof DateTime) {
                    $model->setPublishFrom($dt);
                }

                if (!$model->getPublishTo() instanceof DateTime) {
                    $model->setPublishTo($dt->modify('+10 years'));
                }
            } else {
                $model->setPublishFrom(null);
                $model->setPublishTo(null);
            }
        }
    }
}
