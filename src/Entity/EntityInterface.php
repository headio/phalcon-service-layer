<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Entity;

use Phalcon\Mvc\ModelInterface;

/**
 * Entity Interface
 */
interface EntityInterface extends ModelInterface
{
    /**
     * Return the entity primary key attribute.
     */
    public function getPrimaryKey(): string;

    /**
     * Return the property binding type for a given property.
     */
    public function getPropertyBindType(string $property): int;

    /**
     * Return the model validation errors as an array representation.
     */
    public function getValidationErrors(): array;
}
