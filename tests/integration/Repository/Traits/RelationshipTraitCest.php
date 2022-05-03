<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Repository\Traits;

use Stub\Domain\Repository\Role as Role;
use Stub\Domain\Repository\User as User;
use Stub\Domain\Service\User as Service;
use Stub\Domain\Service\UserInterface as ServiceInterface;
use Phalcon\Mvc\Model\Transaction;
use Phalcon\Mvc\Model\Transaction\Manager;
use IntegrationTester;

class RelationshipTraitCest
{
    private ServiceInterface $service;

    public function _before(IntegrationTester $I): void
    {
        $this->service = new Service(
            new Role(),
            new User(),
        );
    }

    public function canSynchronizeRelations(IntegrationTester $I): void
    {
        $I->wantToTest('synchronizing a related model resultset');

        $data = $this->data();
        $model = $this->service->getModel($data['id']);
        $result = $this->service->synchronizeRoles($model, [2, 3]);

        expect($result)->true();

        $result = $this->service->getRoles($model);

        expect($result->count())->equals(2);
    }

    public function canLinkRelations(IntegrationTester $I): void
    {
        $I->wantToTest('associating a collection of models');

        $data = $this->data();
        $model = $this->service->getModel($data['id']);
        $result = $this->service->linkRoles($model, [2, 3, 4]);

        expect($result)->true();
    }

    public function canUnlinkRelations(IntegrationTester $I): void
    {
        $I->wantToTest('detaching a collection of models');

        $data = $this->data();
        $model = $this->service->getModel($data['id']);
        $result = $this->service->unlinkRoles($model, [1]);

        expect($result)->true();
    }

    private function data(): array
    {
        return [
            'id' => 1,
        ];
    }
}
