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

use Headio\Phalcon\DomainLayer\Filter\{ 
    Filter, 
    FilterInterface,
    GroupBy,
    GroupByInterface,
    OrderBy,
    OrderByInterface 
};
use Stub\Domain\Filter\Role as StubFilter;
use Mockery;
use Module\UnitTest;
use ArrayIterator;

class FilterTest extends UnitTest
{
    protected function _before() : void
    {
        parent::_before();
    }

    protected function _after() : void
    {
        parent::_after();
    }

    public function testCanCreateAnAliasForQueryColumns()
    {
        $this->specify(
            'Can create an alias on query columns',
            function () {
                $mock = Mockery::mock(
                    Filter::class,
                    FilterInterface::class
                );
                $mock->allows()->getAlias()->andReturn($this->_data()['alias']);
                $mock->allows()->hasAlias()->andReturn(true);
                $mock->allows()->alias()->with(Mockery::type('string'))->andReturn(new StubFilter());

                $mock->alias($this->_data()['alias']);

                expect($mock->hasAlias())->true();
                expect($mock->getAlias())->equals($this->_data()['alias']);
                expect_that(is_string($mock->getAlias()));
            }
        );
    }

    public function testCanCreateAColumnConstraint()
    {
        $this->specify(
            'Can create a column constraint',
            function () {
                $mock = Mockery::mock(
                    Filter::class,
                    FilterInterface::class
                );
                $mock->allows()->getColumns()->andReturn($this->_data()['columns']);
                $mock->allows()->hasColumns()->andReturn(true);
                $mock->allows()->columns()->with(Mockery::type('array'))->andReturn(new StubFilter());

                $mock->columns($this->_data()['columns']);

                expect($mock->hasColumns())->true();
                expect($mock->getColumns())->equals($this->_data()['columns']);
                expect_that(is_array($mock->getColumns()));
            }
        );
    }

    public function testCanCreateAnOffsetCondition()
    {
        $this->specify(
            'Can create an offset condition constraint',
            function () {
                $mock = Mockery::mock(
                    Filter::class,
                    FilterInterface::class
                );
                $mock->allows()->getOffset()->andReturn($this->_data()['offsetId']);
                $mock->allows()->hasOffset()->andReturn(true);
                $mock->allows()->offset()->with(Mockery::type('integer'))->andReturn(new StubFilter());

                $mock->offset($this->_data()['offsetId']);

                expect($mock->hasOffset())->true();
                expect($mock->getOffset())->equals($this->_data()['offsetId']);
                expect_that(is_int($mock->getOffset()));
            }
        );
    }

    public function testCanCreateAGroupByConstraint()
    {
        $this->specify(
            'Can create a group by constraint',
            function () {
                $mock = Mockery::mock(
                    Filter::class,
                    FilterInterface::class
                );
                $mock->allows()->getGroupBy()->andReturn(
                    [
                        0 => new GroupBy($this->_data()['groupBy'])
                    ]
                );
                $mock->allows()->hasGroupBy()->andReturn(true);
                $mock->allows()->groupBy()->with(Mockery::type('array'))->andReturn(new StubFilter());

                $mock->groupBy([$this->_data()['groupBy']]);

                expect($mock->hasGroupBy())->true();

                foreach ($mock->getGroupBy() as $item) {
                    expect($item)->isInstanceOf(
                        GroupByInterface::class
                    );
                }
            }
        );
    }

    public function testCanCreateAnOrderByConstraint()
    {
        $this->specify(
            'Can create an order by constraint',
            function () {
                $mock = Mockery::mock(
                    Filter::class,
                    FilterInterface::class
                );
                $mock->allows()->getOrderBy()->andReturn(
                    [
                        0 => new OrderBy($this->_data()['orderBy'])
                    ]
                );
                $mock->allows()->hasOrderBy()->andReturn(true);
                $mock->allows()->orderBy()->with(Mockery::type('array'))->andReturn(new StubFilter());

                expect($mock->hasOrderBy())->true();

                foreach ($mock->getOrderBy() as $item) {
                    expect($item)->isInstanceOf(
                        OrderByInterface::class
                    );
                }
            }
        );
    }

    public function testCanCreateALimitConstraint()
    {
        $this->specify(
            'Can create a limit constraint',
            function () {
                $mock = Mockery::mock(
                    Filter::class,
                    FilterInterface::class
                );
                $mock->allows()->getLimit()->andReturn($this->_data()['limit']);
                $mock->allows()->hasLimit()->andReturn(true);
                $mock->allows()->limit()->with(Mockery::type('integer'))->andReturn(new StubFilter());

                $mock->limit($this->_data()['limit']);

                expect($mock->hasLimit())->true();
                expect($mock->getLimit())->equals($this->_data()['limit']);
                expect_that(is_int($mock->getLimit()));
            }
        );
    }

    /**
     * Return test data
     */
    public function _data() : array
    {
        return [
            'alias' => 'Role',
            'cache' => false,
            'columns' => ['foo', 'bar'],
            'groupBy' => 'label',
            'keyword' => 'Admin',
            'limit' => 50,
            'offsetId' => 25,
            'orderBy' => 'label',
        ];
    }
}
