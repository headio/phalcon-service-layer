<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Service;

use Stub\Domain\Repository\Role as RoleRepository;
use Stub\Domain\Repository\User as UserRepository;
use Stub\Domain\Service\User as Service;
use Stub\Domain\Service\UserInterface as ServiceInterface;
use Phalcon\Mvc\Model\ValidationFailed;
use IntegrationTester;

class ServiceCest
{
    private ServiceInterface $service;

    public function _before(IntegrationTester $I)
    {
        $this->service = new Service(
            new RoleRepository(),
            new UserRepository(),
        );
    }

    public function canInsertRecord(IntegrationTester $I)
    {
        $I->wantToTest('inserting a new record using an isolated transaction');

        $data = $this->data();
        $result = $this->service->createModel($data);

        expect($result)->true();
    }

    public function canUpdateRecord(IntegrationTester $I)
    {
        $I->wantToTest('updating a record using an isolated transaction');

        $model = $this->service->getModel(1);
        $model->setName('Jane Doe');
        $result = $this->service->updateModel($model);

        expect($result)->true();
    }

    public function canDeleteRecord(IntegrationTester $I)
    {
        $I->wantToTest('deleting a record using an isolated transaction');

        $model = $this->service->getModel(1);
        $result = $this->service->deleteModel($model);

        expect($result)->true();
    }

    private function data(): array
    {
        return [
            'name' => 'Baby Doe',
            'email' => 'baby.doe@headcrumbs.io',
            'published' => true
        ];
    }
}
