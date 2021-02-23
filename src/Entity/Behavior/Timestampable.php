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

class Timestampable extends Behavior implements BehaviorInterface
{
    /**
     * {@inheritDoc}
     */
    public function notify($eventType, ModelInterface $model)
    {
        $dt = new DateTime('now');

        if ($eventType === 'beforeValidationOnCreate') {
            $model->setCreated($dt);
            $model->setModified($dt);
        }

        if ($eventType === 'beforeValidationOnUpdate') {
            $model->setModified($dt);
        }
    }
}
