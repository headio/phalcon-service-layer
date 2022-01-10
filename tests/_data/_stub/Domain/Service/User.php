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
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Model\ResultsetInterface;
use Stub\Domain\Repository\RoleInterface;
use Stub\Domain\Repository\UserInterface;
use Stub\Domain\Service\TransactionalCrudTrait;
use Stub\Domain\Service\UserInterface as ServiceInterface;

class User extends Injectable implements ServiceInterface
{
    use TransactionalCrudTrait;

    public function __construct(
        private RoleInterface $roleRepository,
        private UserInterface $repository,
    )
    {
    }

    public function findFirstByEmail(string $email): EntityInterface
    {
        $filter = $this->repository->getQueryFilter()->setEmail($email);

        return $this->repository->findFirst($filter);
    }

    /**
     * Fetch an entity by primary key
     */
    public function getEntity(int $id): EntityInterface
    {
        return $this->repository->findByPk($id);
    }

    public function addModel(array $data): bool
    {
        $entityName = $this->repository->getEntity();
        $model = new $entityName();
        $model->assign($data);

        return $this->insert($model);
    }

    public function deleteModel(EntityInterface $model): bool
    {
        return $this->delete($model);
    }

    public function updateModel(EntityInterface $model): bool
    {
        return $this->update($model);
    }

    /**
     * Return the related roles for a given entity
     */
    public function getRoles(EntityInterface $model): ResultsetInterface
    {
        $filter = $this->roleRepository->getQueryFilter();

        return $this->roleRepository->getRelatedRoles($model, $filter);
    }

    /**
     * Synchronize role relationships
     *
     * @throws \Phalcon\Mvc\Model\Transaction\Failed
     */
    public function synchronizeRoles(EntityInterface $model, array $keys): bool
    {
        $transaction = $this->transactionManager->get();
        $model->setTransaction($transaction);

        if (false === $this->roleRepository->synchronize('roles', 'userRoles', $model, $keys)) {
            $transaction->rollback('Unable to synchronize role relationships.', $model);
        }

        if (false === $model->update()) {
            $transaction->rollback('Unable to update record.', $model);
        }

        $transaction->commit();

        return true;
    }

    /**
     * Associate a collection of role entities
     *
     * @throws \Phalcon\Mvc\Model\Transaction\Failed
     */
    public function linkRoles(EntityInterface $model, array $keys): bool
    {
        if ($this->roleRepository->link('roles', $model, $keys)) {
            return $this->update($model);
        }

        return false;
    }

    /**
     * Detach a collection of related roles
     *
     * @throws \Phalcon\Mvc\Model\Transaction\Failed
     */
    public function unlinkRoles(EntityInterface $model, array $keys): bool
    {
        $transaction = $this->transactionManager->get();
        $model->setTransaction($transaction);

        if (false === $this->roleRepository->unlink('roles', $model, $keys, $transaction)) {
            $transaction->rollback('Unable to delete record.', $model);
        }

        $transaction->commit();

        return true;
    }
}
