<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Model;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Stub\Domain\Repository\User;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\ValidationFailed;
use IntegrationTester;

class ModelCest
{
    private User $repository;

    public function _before(IntegrationTester $I)
    {
        $this->repository = new User();
    }

    public function canGetPrimarykey(IntegrationTester $I): void
    {
        $I->wantToTest('fetching the primary key attribute value definition');

        $modelName = $this->repository->getModel();
        $model = new $modelName();

        $I->assertEquals(
            $model->getPrimarykey(),
            'id'
        );
    }

    public function testGetPropertyBindType(IntegrationTester $I): void
    {
        $I->wantToTest('fetching a given attribute bind type definition');
 
        $modelName = $this->repository->getModel();
        $model = new $modelName();

        $I->assertEquals(
            $model->getPropertyBindType('name'),
            Column::BIND_PARAM_STR
        );
    }

    public function testCreateCriteria(IntegrationTester $I): void
    {
        $I->wantToTest('fetching an instance of the criteria interface for a given repository');

        $modelName = $this->repository->getModel();

        $I->assertInstanceOf(
            CriteriaInterface::class,
            $modelName::query(
                $I->getApplication()->getDI()
            )
        );
    }

    public function canReturnValidationErrors(IntegrationTester $I): void
    {
        $I->wantToTest('invoking the model validation to inspect the validation errors');

        $modelName = $this->repository->getModel();
        $model = new $modelName();
        $model->assign(
            $this->data()
        );

        try {
            $result = $model->create();
        } catch (ValidationFailed) {
            $result = $model->getValidationErrors();
            expect_that(is_array($result));
            expect($result)->hasKey('email');
        }
    }

    private function data(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'hasNoEmail',
        ];
    }
}
