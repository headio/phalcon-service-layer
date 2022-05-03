<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Domain\Service;

use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

interface UserInterface
{
    public function createModel(array $data): bool;

    public function deleteModel(ModelInterface $model): bool;

    public function getModel(int $id): ModelInterface;

    public function updateModel(ModelInterface $model): bool;

    public function getRoles(ModelInterface $model): ResultsetInterface;

    public function synchronizeRoles(ModelInterface $model, array $keys): bool;

    public function linkRoles(ModelInterface $model, array $keys): bool;

    public function unlinkRoles(ModelInterface $model, array $keys): bool;
}
