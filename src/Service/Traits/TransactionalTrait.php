<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Service\Traits;

use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Phalcon\Mvc\Model\Transaction\Failed as TransactionFailed;

/**
 * A simple transactional trait to perform isolated transactions.
 *
 * @property \Phalcon\Mvc\Model\TransactionInterface $transactionManager
 */
trait TransactionalTrait
{
    /**
     * Delete an existing record; returns true on success
     * and throws a transaction failed exception on failure.
     *
     * @throws TransactionFailed
     */
    protected function delete(ModelInterface $model): bool
    {
        $transaction = $this->transactionManager
            ->get()
            ->throwRollbackException(true);
        $model->setTransaction($transaction);

        if (false === $model->delete()) {
            $transaction->rollback('Unable to delete record.', $model);
        }

        $transaction->commit();

        return true;
    }

    /**
     * Persist a new model; returns true on success
     * and throws a transaction failed exception on failure.
     *
     * @throws TransactionFailed
     */
    protected function insert(ModelInterface $model): bool
    {
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
     * Update an existing model instance; returns true on success
     * and throws a transaction failed exception on failure.
     *
     * @throws TransactionFailed
     */
    protected function update(ModelInterface $model): bool
    {
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
