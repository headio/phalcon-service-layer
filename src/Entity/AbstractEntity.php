<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Entity;

use Headio\Phalcon\ServiceLayer\Exception\OutOfRangeException;
use Phalcon\Mvc\Model;
use function sprintf;

class AbstractEntity extends Model implements EntityInterface
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

        if (!isset($metaData)) {
            $metaData = $this->getDI()->get('modelsMetadata');
        }

        if (!$metaData->hasAttribute($this, $property)) {
            throw new OutOfRangeException(
                sprintf('Unknown property %s', $property)
            );
        }

        $bindTypes = $metaData->getBindTypes($this);

        return $bindTypes[$property];
    }

    /**
     * Return the model validation errors as an array representation,
     * consolidating individual field validation errors.
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if ($this->validationHasFailed()) {
            foreach ($this->getMessages() as $message) {
                if (isset($errors[$message->getField()])) {
                    $errors[$message->getField()] .= ' ' . $message->getmessage();
                } else {
                    $errors[$message->getField()] = $message->getmessage();
                }
            }
        }

        return $errors;
    }
}
