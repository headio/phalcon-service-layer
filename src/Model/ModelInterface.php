<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Model;

use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Phalcon\Di\DiInterface;

interface ModelInterface extends \Phalcon\Mvc\ModelInterface
{
    /**
     * Return the primary key attribute.
     */
    public function getPrimaryKey(): string;

    /**
     * Return the property binding type for a given property.
     */
    public function getPropertyBindType(string $property): int;

    /**
     * Return the validation errors as an array representation,
     * consolidating individual field validation errors.
     */
    public function getValidationErrors(): array;

    /**
     * Return an instance of the query criteria pre-populated
     * with the model from which the method was called in.
     */
    public static function query(DiInterface $container = null): CriteriaInterface;
}
