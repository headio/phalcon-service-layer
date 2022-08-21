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
use Headio\Phalcon\ServiceLayer\Service\Traits\TransactionalTrait;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Model\ResultsetInterface;
use Stub\Domain\Repository\RoleInterface;
use Stub\Domain\Repository\UserInterface;
use Stub\Domain\Service\UserInterface as ServiceInterface;

class User extends Injectable implements ServiceInterface
{
    use TransactionalTrait;

    public function __construct(
        private RoleInterface $roleRepository,
        private UserInterface $repository,
    ) {
    }

    public function createModel(array $data): bool
    {
        $model = $this->repository->newInstance();
        $model->assign($data);

        return $this->insert($model);
    }

    public function deleteModel(ModelInterface $model): bool
    {
        return $this->delete($model);
    }

    public function getModel(int $id): ModelInterface
    {
        return $this->repository->findByPk($id);
    }

    public function updateModel(ModelInterface $model): bool
    {
        return $this->update($model);
    }

    public function getRoles(ModelInterface $model): ResultsetInterface
    {
        return $this->repository->getRoles($model);
    }

    /**
     * @throws \Phalcon\Mvc\Model\Transaction\Failed
     */
    public function synchronizeRoles(ModelInterface $model, array $keys): bool
    {
        $transaction = $this->transactionManager
            ->get()
            ->throwRollbackException(true);
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
     * @throws \Phalcon\Mvc\Model\Transaction\Failed
     */
    public function linkRoles(ModelInterface $model, array $keys): bool
    {
        if ($this->roleRepository->link('roles', $model, $keys)) {
            return $this->update($model);
        }

        return false;
    }

    /**
     * @throws \Phalcon\Mvc\Model\Transaction\Failed
     */
    public function unlinkRoles(ModelInterface $model, array $keys): bool
    {
        $transaction = $this->transactionManager
            ->get()
            ->throwRollbackException(true);
        $model->setTransaction($transaction);

        if (false === $this->roleRepository->unlink('roles', $model, $keys, $transaction)) {
            $transaction->rollback('Unable to delete record.', $model);
        }

        $transaction->commit();

        return true;
    }
}
