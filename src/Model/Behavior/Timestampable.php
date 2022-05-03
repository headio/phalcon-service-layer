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

class Timestampable extends Behavior implements BehaviorInterface
{
    /**
     * {@inheritDoc}
     */
    public function notify(string $type, ModelInterface $model): void
    {
        $dt = new DateTimeImmutable('now');

        if ($type === 'beforeValidationOnCreate') {
            $model->setCreated($dt);
            $model->setModified($dt);
        }

        if ($type === 'beforeValidationOnUpdate') {
            $model->setModified($dt);
        }
    }
}
