<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Unit\Filter;

use Headio\Phalcon\ServiceLayer\Filter\Join;
use Headio\Phalcon\ServiceLayer\Filter\JoinInterface;
use Stub\Domain\Entity\Role;
use Stub\Domain\Entity\User;
use Stub\Domain\Entity\RoleUser;
use Mockery;
use Module\UnitTest;
use UnitTester;

class JoinTest extends UnitTest
{
    public function testCanInitializeJoin(): void
    {
        $this->specify(
            'Can initialize a join object and return the respective class properties',
            function () {
                $args = array_values(
                    $this->_data()
                );
                $mock = Mockery::mock(
                    Join::class,
                    [...$args]
                );
                $mock->shouldReceive('getEntity')->andReturn(Mockery::type('string'));
                $mock->shouldReceive('getConstraint')->andReturn(Mockery::type('string'));
                $mock->shouldReceive('getAlias')->andReturn(Mockery::type('string'));
                $mock->shouldReceive('getType')->andReturn(Mockery::type('string'));

                $mock->getEntity();
                $mock->getConstraint();
                $mock->getType();
                $mock->getAlias();
            }
        );
    }

    private function _data(): array
    {
        return [
            'entity' => User::class,
            'constraint' => User::class . '.id = ' . RoleUser::class . '.user_id',
            'type' => JOIN::INNER,
        ];
    }
}
