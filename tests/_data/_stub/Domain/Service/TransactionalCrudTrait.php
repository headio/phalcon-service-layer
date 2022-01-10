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
    protected function delete(EntityInterface $model): bool
    {
        /** @var \Phalcon\Mvc\Model\TransactionInterface */
        $transaction = $this->transactionManager
            ->get()
            ->throwRollbackException(true);
        /*
        $transaction = $this->transactionManager->setDbService(
            $model->getWriteConnectionService()
        )->get()->throwRollbackException(true);
        */
        $model->setTransaction($transaction);

        if (false === $model->delete()) {
            $transaction->rollback('Unable to delete record.', $model);
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
    protected function insert(EntityInterface $model): bool
    {
        /** @var \Phalcon\Mvc\Model\TransactionInterface */
        $transaction = $this->transactionManager->get()
            ->throwRollbackException(true);
        $model->setTransaction($transaction);

        if (false === $model->create()) {
            $transaction->rollback('Unable to create new record.', $model);
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
    protected function update(EntityInterface $model): bool
    {
        /** @var \Phalcon\Mvc\Model\TransactionInterface */
        $transaction = $this->transactionManager->get()
            ->throwRollbackException(true);
        $model->setTransaction($transaction);

        if (false === $model->update()) {
            $transaction->rollback('Unable to update record.', $model);
        }

        $transaction->commit();

        return true;
    }
}
