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
use Phalcon\Mvc\Model\Transaction\Failed as TransactionFailed;

/**
 * A simple transactional crud trait to perform isolated transactions.
 *
 * @note:
 * For complex services spanning multiple repositories, implement your
 * own methods to handle transactional persistence logic.
 */
trait TransactionalCrudTrait
{
    /**
     * Delete an existing entity; returns true on success
     * and throws a transaction failed exception on failure.
     *
     * @throws TransactionFailed
     */
    protected function delete(EntityInterface $entity): bool
    {
        $transaction = $this->transactionManager->get();
        $entity->setTransaction($transaction);

        if (false === $entity->delete()) {
            $transaction->rollback('Unable to delete record.', $entity);
        }

        $transaction->commit();

        return true;
    }

    /**
     * Persist a new entity; returns true on success
     * and throws a transaction failed exception on failure.
     *
     * @throws TransactionFailed
     */
    protected function insert(EntityInterface $entity): bool
    {
        $transaction = $this->transactionManager->get();
        $entity->setTransaction($transaction);

        if (false === $entity->create()) {
            $transaction->rollback('Unable to create new record.', $entity);
        }

        $transaction->commit();

        return true;
    }

    /**
     * Update an existing entity; returns true on success
     * and throws a transaction failed exception on failure.
     *
     * @throws TransactionFailed
     */
    protected function update(EntityInterface $entity): bool
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
