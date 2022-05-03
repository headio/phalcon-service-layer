<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Unit\Repository\Traits;

use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Stub\Domain\Filter\Role as QueryFilter;
use Stub\Domain\Model\Role as Model;
use Stub\Domain\Repository\Role as Repository;
use Mockery;
use Module\UnitTest;

class RelationshipTraitTest extends UnitTest
{

    private $mock;

    protected function _before(): void
    {
        parent::_before();

        $this->mock = Mockery::mock(
            Repository::class,
        )
        ->makePartial();

        $this->mock->allows()->link()
        ->with(
            Mockery::type('string'),
            Model::class,
            Mockery::type('array')
        )
        ->andReturn(true);

        $this->mock->allows()->unlink()
        ->with(
            Mockery::type('string'),
            Model::class,
            Mockery::type('array'),
        )
        ->andReturn(true);

        $this->mock->allows()->synchronize()
        ->with(
            Mockery::type('string'),
            Mockery::type('string'),
            Model::class,
            Mockery::type('array'),
        )
        ->andReturn(true);
    }

    protected function _after(): void
    {
        Mockery::close();
        parent::_after();
    }

    public function testCanLinkACollectionOfModels(): void
    {
        $result = $this->mock->link('users', new Model(), [1,2,3,4,5,6]);

        expect_that(is_bool($result));
    }

    public function testCanUnlinkACollectionOfModels(): void
    {
        $result = $this->mock->unlink('users', new Model(), [1,2,3,4,5,6], null);

        expect_that(is_bool($result));
    }

    public function testCanSynchronizeRelationshipsBetweenTwoEntities(): void
    {
        $result = $this->mock->synchronize(
            'users',
            'roleUsers',
            new Model(),
            [1,2,3,4,5,6],
        );

        expect_that(is_bool($result));
    }
}
