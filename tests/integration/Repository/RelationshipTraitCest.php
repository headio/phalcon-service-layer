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

namespace Integration\Repository;

use Stub\Domain\Repository\Role as RoleRepository;
use Stub\Domain\Repository\User as UserRepository;
use Stub\Domain\Service\User as Service;
use IntegrationTester;

class RelationshipTraitCest
{
    private $service;

    public function _before(IntegrationTester $I)
    {
        $this->service = new Service(
            new RoleRepository(false),
            new UserRepository(false)
        );
    }

    public function canSynchronizeRelations(IntegrationTester $I)
    {
        $I->wantTo('Synchronize a model relationship');

        $entity = $this->service->getEntity(1);
        $result = $this->service->synchronizeRoles($entity, [2,3]);

        expect($result)->true();

        $result = $this->service->getRoles($entity);

        expect($result->count())->equals(2);
    }

    public function canLinkRelations(IntegrationTester $I)
    {
        $I->wantToTest('Associating a collection of models');

        $entity = $this->service->getEntity(1);
        $result = $this->service->linkRoles($entity, [2,3,4]);

        expect($result)->true();
    }

    public function canUnlinkRelations(IntegrationTester $I)
    {
        $I->wantToTest('detach a collection of models');

        $entity = $this->service->getEntity(1);
        $result = $this->service->unlinkRoles($entity, [1]);

        expect($result)->true();
    }
}
