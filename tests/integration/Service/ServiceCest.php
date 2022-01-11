<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Service;

use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Stub\Domain\Repository\Role as RoleRepository;
use Stub\Domain\Repository\User as UserRepository;
use Stub\Domain\Service\User as Service;
use Stub\Domain\Service\UserInterface as ServiceInterface;
use Phalcon\Mvc\Model\ValidationFailed;
use Phalcon\Mvc\Model\Transaction\Failed as TransactionFailed;
use IntegrationTester;

class ServiceCest
{
    private ServiceInterface $service;

    public function _before(IntegrationTester $I)
    {
        $this->service = new Service(
            new RoleRepository(false),
            new UserRepository(false),
        );
    }

    public function canFindRecordByProperty(IntegrationTester $I)
    {
        $I->wantTo('find a record by property');

        $data = $this->getData();
        $result = $this->service->addModel($data);

        expect($result)->true();

        $result = $this->service->findFirstByEmail($data['email']);
    }

    public function canFindRecordByPk(IntegrationTester $I)
    {
        $I->wantTo('find a record by pk');

        $result = $this->service->getModel(1);

        expect($result)->isInstanceOf(EntityInterface::class);
    }

    public function canInsertRecord(IntegrationTester $I)
    {
        $I->wantToTest('inserting a new record using an isolated transaction');

        $data = $this->getData();
        $result = $this->service->addModel($data);

        expect($result)->true();
    }

    public function canUpdateRecord(IntegrationTester $I)
    {
        $I->wantToTest('updating an existing record using an isolated transaction');

        $data = $this->getData();
        $this->service->addModel($data);
        $model = $this->service->findFirstByEmail($data['email']);
        $model->setName('Jane Doe');
        $result = $this->service->updateModel($model);

        expect($result)->true();
    }

    public function canDeleteRecord(IntegrationTester $I)
    {
        $I->wantToTest('deleting a record using an isolated transaction');

        $data = $this->getData();
        $this->service->addModel($data);
        $model = $this->service->findFirstByEmail($data['email']);
        $result = $this->service->deleteModel($model);

        expect($result)->true();
    }

    private function getData(): array
    {
        return [
            'name' => 'Baby Doe',
            'email' => 'baby.doe@headcrumbs.io',
            'published' => true
        ];
    }
}
