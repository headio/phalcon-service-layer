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
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\ValidationFailed;
use IntegrationTester;

class EntityCest
{
    private User $repository;

    public function _before(IntegrationTester $I)
    {
        $this->repository = new User(false);
    }

    public function canGetPrimarykey(IntegrationTester $I): void
    {
        $I->wantToTest('a model returns the expected primary key attribute value');
        $entityName = $this->repository->getEntity();
        $model = new $entityName();

        $I->assertEquals(
            $model->getPrimarykey(),
            'id'
        );
    }

    public function testGetPropertyBindType(IntegrationTester $I): void
    {
        $I->wantToTest('a model returns the expected attribute bind type');
        $entityName = $this->repository->getEntity();
        $model = new $entityName();

        $I->assertEquals(
            $model->getPropertyBindType('name'),
            Column::BIND_PARAM_STR
        );
    }

    public function canReturnValidationErrors(IntegrationTester $I): void
    {
        $I->wantToTest('invoking the model validation to inspect the validation errors');

        $entityName = $this->repository->getEntity();
        $model = new $entityName();
        $model->assign(
            $this->getData()
        );

        try {
            $result = $model->create();
        } catch (ValidationFailed) {
            $result = $model->getValidationErrors();
            expect_that(is_array($result));
            expect($result)->hasKey('email');
        }
    }

    private function getData(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'hasNoEmail',
        ];
    }
}
