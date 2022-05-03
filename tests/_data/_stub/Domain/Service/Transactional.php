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
use Phalcon\Mvc\Model\Transaction\Failed as TransactionFailed;

/**
 * A simple transactional trait to perform isolated transactions.
 *
 * @note:
 * For complex services spanning multiple repositories, implement your
 * own methods to handle transactional persistence logic.
 */
trait Transactional
{
    /**
     * Delete an existing record; returns true on success
     * and throws a transaction failed exception on failure.
     *
     * @throws TransactionFailed
     */
    protected function delete(ModelInterface $model): bool
    {
        /** @var \Phalcon\Mvc\Model\TransactionInterface */
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
     * Update an existing model instance; returns true on success
     * and throws a transaction failed exception on failure.
     *
     * @throws TransactionFailed
     */
    protected function update(ModelInterface $model): bool
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
