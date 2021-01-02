<?php
/*
 * This source file is subject to the MIT License.
 *
 * (c) Dominic Beck <dominic@headcrumbs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Entity;

use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Stub\Domain\Repository\User;
use Stub\Domain\Repository\UserInterface;
use Phalcon\Mvc\Model\ValidationFailed;
use IntegrationTester;

class EntityCest
{
    private UserInterface $repository;

    public function _before(IntegrationTester $I)
    {
        $this->repository = new user(false);
        $this->di = $I->getApplication()->getDI();
    }

    public function canReturnValidationErrors(IntegrationTester $I)
    {
        $I->wantTo('Return the entity validation errors.');

        $entityName = $this->repository->getEntity();
        $entity = new $entityName();
        $entity->assign(
            $this->getData()
        );

        try {
            $this->insert($entity);
        } catch (ValidationFailed $e) {
            $result = $entity->getValidationErrors();

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
    private function insert(EntityInterface $entity): bool
    {
        $transaction = $this->di->get('transactionManager')->get();
        $entity->setTransaction($transaction);

        if (false === $entity->create()) {
            $transaction->rollback('Unable to create new record.', $entity);
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
