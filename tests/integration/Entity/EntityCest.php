<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Entity;

use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Stub\Domain\Repository\User;
use Phalcon\Mvc\Model\ValidationFailed;
use Phalcon\Mvc\Model\Transaction\Failed as TransactionFailed;
use IntegrationTester;

class EntityCest
{
    private User $repository;

    public function _before(IntegrationTester $I)
    {
        $this->repository = new User(false);
        $this->di = $I->getApplication()->getDI();
    }

    public function canReturnValidationErrors(IntegrationTester $I)
    {
        $I->wantToTest('invoking the model validation logic to inspect the validation errors.');

        $entityName = $this->repository->getEntity();
        $model = new $entityName();
        $model->assign(
            $this->getData()
        );

        try {
            $result = $this->insert($model);
        } catch (ValidationFailed) {
            $result = $model->getValidationErrors();
            expect_that(is_array($result));
            expect($result)->hasKey('email');
        }
    }

    /**
     * Persist a new entity; returns true on success
     * and throws a transaction failed exception on failure.
     *
     * @throws TransactionFailed
     */
    private function insert(EntityInterface $model): bool
    {
        /** @var \Phalcon\Mvc\Model\TransactionInterface */
        $transaction = $this->di->get('transactionManager')
            ->get()
            ->throwRollbackException(false);
        $model->setTransaction($transaction);

        if (false === $model->create()) {
            $transaction->rollback('Unable to create new record.', $model);
        }

        $transaction->commit();

        return true;
    }

    private function getData(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'hasNoEmail',
        ];
    }
}
