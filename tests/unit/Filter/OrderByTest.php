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

namespace Unit\Filter;

use Headio\Phalcon\ServiceLayer\Filter\OrderBy;
use Headio\Phalcon\ServiceLayer\Filter\OrderByInterface;
use Mockery;
use Module\UnitTest;

class OrderByTest extends UnitTest
{
    protected function _before(): void
    {
        parent::_before();
    }

    protected function _after(): void
    {
        parent::_after();
    }

    public function testCanCreateOrderByConstraintUsingDefaultDirection(): void
    {
        $this->specify(
            'Can create an order by constraint using the default direction',
            function () {
                $operator = OrderByInterface::ASC;
                $mock = Mockery::mock(
                    OrderBy::class,
                    OrderByInterface::class,
                    [
                        $this->_data()['column']
                    ]
                );

                $mock->allows()->getColumn()->andReturn($this->_data()['column']);
                $mock->allows()->getDirection()->andReturn(null);
                $mock->allows()->hasDirection()->andReturn(false);

                expect($mock->getColumn())->equals($this->_data()['column']);
                expect($mock->getDirection())->equals(null);
                expect($mock->hasDirection())->equals(false);
            }
        );
    }

    public function testCanCreateOrderByConstraintUsingDirection(): void
    {
        $this->specify(
            'Can create an order by constraint using explicit direction',
            function () {
                $mock = Mockery::mock(
                    OrderBy::class,
                    OrderByInterface::class,
                    [
                        $this->_data()['column'],
                        OrderByInterface::DESC
                    ]
                );

                $mock->allows()->getColumn()->andReturn($this->_data()['column']);
                $mock->allows()->getDirection()->andReturn(OrderByInterface::DESC);
                $mock->allows()->hasDirection()->andReturn(true);

                expect($mock->getColumn())->equals($this->_data()['column']);
                expect($mock->getDirection())->equals(OrderByInterface::DESC);
                expect($mock->hasDirection())->true();
            }
        );
    }

    /**
     * Return test data
     */
    public function _data(): array
    {
        return [
            'column' => 'name',
        ];
    }
}
