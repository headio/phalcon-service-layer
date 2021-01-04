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
    private RoleInterface $roleRepository;

    private UserInterface $repository;

    use TransactionalCrudTrait;

    public function __construct(RoleInterface $roleRepository, UserInterface $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->repository = $userRepository;
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
        $entity = new $entityName();
        $entity->assign($data);

        return $this->insert($entity);
    }

    public function deleteModel(EntityInterface $entity): bool
    {
        return $this->delete($entity);
    }

    public function updateModel(EntityInterface $entity): bool
    {
        return $this->update($entity);
    }

    /**
     * Return the related roles for a given entity
     */
    public function getRoles(EntityInterface $entity): ResultsetInterface
    {
        $filter = $this->roleRepository->getQueryFilter();

        return $this->roleRepository->getRelatedRoles($entity, $filter);
    }

    /**
     * Synchronize role relationships
     *
     * @throws Phalcon\Mvc\Model\Transaction\Failed
     */
    public function synchronizeRoles(EntityInterface $entity, array $keys): bool
    {
        $transaction = $this->transactionManager->get();
        $entity->setTransaction($transaction);

        if (false === $this->roleRepository->synchronize('roles', 'userRoles', $entity, $keys)) {
            $transaction->rollback('Unable to synchronize role relationships.', $entity);
        }

        if (false === $entity->update()) {
            $transaction->rollback('Unable to update record.', $entity);
        }

        $transaction->commit();

        return true;
    }

    /**
     * Associate a collection of role entities
     *
     * @throws Phalcon\Mvc\Model\Transaction\Failed
     */
    public function linkRoles(EntityInterface $entity, array $keys): bool
    {
        if ($this->roleRepository->link('roles', $entity, $keys)) {
            return $this->update($entity);
        }

        return false;
    }

    /**
     * Detach a collection of related roles
     *
     * @throws Phalcon\Mvc\Model\Transaction\Failed
     */
    public function unlinkRoles(EntityInterface $entity, array $keys): bool
    {
        $transaction = $this->transactionManager->get();
        $entity->setTransaction($transaction);

        if (false === $this->roleRepository->unlink('roles', $entity, $keys, $transaction)) {
            $transaction->rollback('Unable to delete record.', $entity);
        }

        $transaction->commit();

        return true;
    }
}
