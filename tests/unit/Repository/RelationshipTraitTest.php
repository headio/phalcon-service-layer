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

namespace Unit\Repository;

use Headio\Phalcon\DomainLayer\Filter\FilterInterface;
use Headio\Phalcon\DomainLayer\Repository\RepositoryInterface;
use Headio\Phalcon\DomainLayer\Repository\RelationshipTrait;
use Headio\Phalcon\DomainLayer\Repository\QueryRepository;
use Stub\Domain\Filter\Role as QueryFilter;
use Stub\Domain\Entity\Role as Entity;
use Stub\Domain\Repository\Role as Repository;
use Phalcon\Mvc\Model\Transaction; 
use Phalcon\Mvc\Model\Transaction\Manager;
use Mockery;
use Module\UnitTest;

class RelationshipTraitTest extends UnitTest
{
    /**
     * @var Mockery
     */
    private $mock;

    protected function _before() : void
    {
        parent::_before();

        $this->mock = Mockery::mock(
            RepositoryInterface::class,
            Repository::class,
            [false]
        )
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();

        $this->mock->allows()
            ->link()
            ->with(Mockery::type('string'), Entity::class, Mockery::type('array'))
            ->andReturn(true);

        $this->mock->allows()
            ->unlink()
            ->with(
                Mockery::type('string'),
                Entity::class, 
                Mockery::type('array'), 
                Transaction::class
            )
            ->andReturn(true);

        $this->mock->allows()
            ->synchronize()
            ->with(
                Mockery::type('string'),
                Mockery::type('string'), 
                Entity::class, 
                Mockery::type('array'), 
                Transaction::class
            )
            ->andReturn(true);
    }

    protected function _after() : void
    {
        parent::_after();
    }

    public function testLinkModel(): void
    {
        $result = $this->mock->link('users', new Entity, [1,2,3,4,5,6]);

        expect_that(is_bool($result));
    }

    public function testUnlinkModel(): void
    {
        $result = $this->mock->unlink('users', new Entity, [1,2,3,4,5,6], null);

        expect_that(is_bool($result));
    }

    public function testUnlinkModelWithTransaction(): void
    {
        $manager = new Manager;
        $transaction = $manager->get();
        $result = $this->mock->link('users', new Entity, [1,2,3,4,5,6], $transaction);

        expect_that(is_bool($result));
    }

    public function testSynchronizeRelationship(): void
    {
        $result = $this->mock->synchronize(
            'users',
            'roleUsers', 
            new Entity, 
            [1,2,3,4,5,6],
            null
        );

        expect_that(is_bool($result));
    }

    public function testSynchronizeRelationshipWithTransaction(): void
    {
        $manager = new Manager;
        $transaction = $manager->get();
        $result = $this->mock->synchronize(
            'users', 
            'roleUsers', 
            new Entity, 
            [1,2,3,4,5,6], 
            $transaction
        );

        expect_that(is_bool($result));
    }
}
