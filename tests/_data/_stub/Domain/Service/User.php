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
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Model\ResultsetInterface;
use Stub\Domain\Repository\RoleInterface;
use Stub\Domain\Repository\UserInterface;

class User extends Injectable
{
    private $roleRepository;

    private $repository;

    public function __construct(RoleInterface $roleRepository, UserInterface $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->repository = $userRepository;
    }

    /**
     * Fetch an entity by primary key
     */
    public function getEntity(int $id): EntityInterface
    {
        return $this->repository->findByPk($id);
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

        // Do some more logic

        // Commit
        $transaction->commit();

        return true;
    }

    /**
     * Update an existing entity; returns true on success
     * and throws a transaction failed exception on failure.
     *
     * @throws Phalcon\Mvc\Model\Transaction\Failed
     */
    private function update(EntityInterface $entity): bool
    {
        $transaction = $this->transactionManager->get();
        $entity->setTransaction($transaction);

        if (false === $entity->update()) {
            $transaction->rollback('Unable to update record.', $entity);
        }

        $transaction->commit();

        return true;
    }
}
