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

use Headio\Phalcon\ServiceLayer\Repository\RepositoryInterface;
use Stub\Domain\Entity\Role as Entity;
use Stub\Domain\Filter\Role as Filter;
use Stub\Domain\Repository\Role as Repository;
use Mockery;
use Module\UnitTest;

class RepositoryTest extends UnitTest
{
    private $mock;

    protected function _before() : void
    {
        parent::_before();

        $this->mock = Mockery::mock(
            Repository::class,
            RepositoryInterface::class,
            [false]
        )
        ->makePartial();

        $this->mock->allows()->getEntity()->andReturn(Entity::class);
        $this->mock->allows()->getQueryFilter()->andReturn(new Filter());
    }

    protected function _after() : void
    {
        parent::_after();
    }

    public function testNoCache() : void
    {
        $this->specify(
            'Query repository is configured not to utilize caching',
            function () {
                $prop = $this->getClassProperty(get_class($this->mock), 'cache');
                $result = $prop->getValue($this->mock);
                expect($result)->false();
            }
        );
    }

    public function testUsingCache() : void
    {
        $this->specify(
            'Query repository is configured to utilize caching',
            function () {
                $repository = Mockery::mock(
                    Repository::class,
                    RepositoryInterface::class,
                    [true]
                )
                ->makePartial();
                $prop = $this->getClassProperty(get_class($repository), 'cache');
                $result = $prop->getValue($repository);

                expect($result)->true();
            }
        );
    }

    public function testGetEntity() : void
    {
        $this->specify(
            'Return the entity managed by the repository',
            function () {
                $result = $this->mock->getEntity();
                expect($result)->equals(Entity::class);
            }
        );
    }

    public function testGetQueryFilter() : void
    {
        $this->specify(
            'Return the query filter assigned to the repository',
            function () {
                $result = $this->mock->getQueryFilter();
                expect($result)->isInstanceOf(Filter::class);
            }
        );
    }
}
