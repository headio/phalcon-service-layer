<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Service;

use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

interface UserInterface
{
    public function findFirstByEmail(string $email): EntityInterface;

    public function getEntity(int $id): EntityInterface;

    public function addModel(array $data): bool;

    public function deleteModel(EntityInterface $model): bool;

    public function updateModel(EntityInterface $model): bool;

    public function getRoles(EntityInterface $model): ResultsetInterface;

    public function synchronizeRoles(EntityInterface $model, array $keys): bool;

    public function linkRoles(EntityInterface $model, array $keys): bool;

    public function unlinkRoles(EntityInterface $model, array $keys): bool;
}
