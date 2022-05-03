<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Model;

use Headio\Phalcon\ServiceLayer\Exception\OutOfRangeException;
use Headio\Phalcon\ServiceLayer\Model\Criteria;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Phalcon\Di\Di;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Model;
use function sprintf;

abstract class AbstractModel extends Model implements ModelInterface
{
    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        self::setup(
            [
                'castOnHydrate' => true,
                'notNullValidations' => false
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getPrimaryKey(): string
    {
        return $this->getDI()->get('modelsMetadata')->getIdentityField($this);
    }

    /**
     * {@inheritDoc}
     *
     * @throws OutOfRangeException
     */
    public function getPropertyBindType(string $property): int
    {
        static $metaData = null;

        $metaData ??= $this->getDI()->get('modelsMetadata');

        if (!$metaData->hasAttribute($this, $property)) {
            throw new OutOfRangeException(
                sprintf('Unknown property "%s"', $property)
            );
        }

        $bindTypes = $metaData->getBindTypes($this);

        return $bindTypes[$property];
    }

    /**
     * {@inheritDoc}
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if ($this->validationHasFailed()) {
            foreach ($this->getMessages() as $message) {
                if (isset($errors[$message->getField()])) {
                    $errors[$message->getField()] .= ' ' . $message->getMessage();
                } else {
                    $errors[$message->getField()] = $message->getMessage();
                }
            }
        }

        return $errors;
    }

    /**
     * {@inheritDoc}
     */
    public static function query(DiInterface $container = null): CriteriaInterface
    {
        $container ??= Di::getDefault();
        $criteria = $container->get(Criteria::class) ?? new Criteria();
        $criteria->setDI($container);
        $criteria->setModelName(
            get_called_class()
        );

        return $criteria;
    }
}
