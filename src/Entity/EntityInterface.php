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

namespace Headio\Phalcon\ServiceLayer\Entity;

/**
 * Entity Interface
 */
interface EntityInterface
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
