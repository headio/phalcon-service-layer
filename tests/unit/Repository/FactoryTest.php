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

use Headio\Phalcon\ServiceLayer\Exception\InvalidArgumentException;
use Headio\Phalcon\ServiceLayer\Repository\RepositoryInterface;
use Headio\Phalcon\ServiceLayer\Repository\Factory;
use Headio\Phalcon\ServiceLayer\Repository\FactoryInterface;
use Stub\Domain\Repository\Role as Repository;
use Mockery;
use Module\UnitTest;

class FactoryTest extends UnitTest
{
    /**
     * @var Mockery
     */
    private $mock;

    protected function _before() : void
    {
        parent::_before();

        $cache = false;
        $this->mock = Mockery::mock(
            Factory::class,
            FactoryInterface::class
        );
        $this->mock->allows()
            ->create()
            ->with(Mockery::type('string'), Mockery::type('bool'))
            ->andReturnUsing(function ($class) use ($cache) {
                if (!class_exists($class)) {
                    throw new InvalidArgumentException(
                        sprintf('Repository class %s does not exist.', $class)
                    );
                }

                return new $class($cache);
            });
    }

    protected function _after() : void
    {
        parent::_after();
    }

    public function testFactoryInstantiationWithValidRepository() : void
    {
        $this->specify(
            'Repository manager can instantiate requested repository',
            function () {
                $repository = $this->mock->create(
                    Repository::class,
                    false
                );

                expect($repository)->isInstanceOf(Repository::class);

                expect($repository)->isInstanceOf(RepositoryInterface::class);
            }
        );
    }

    public function testFactoryInstantiationWithInvalidRepository() : void
    {
        $this->specify(
            'Exception is thrown instantiating unknown repository',
            function () {
                $this->expectException(InvalidArgumentException::class);

                $this->expectExceptionMessage('Repository class Unit\Repository\Foo does not exist.');

                $repository = $this->mock->create(
                    Foo::class,
                    false
                );
            }
        );
        
    }
}
