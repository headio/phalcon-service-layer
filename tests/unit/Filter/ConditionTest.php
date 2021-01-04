<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Unit\Filter;

use Headio\Phalcon\ServiceLayer\Filter\Condition;
use Headio\Phalcon\ServiceLayer\Filter\ConditionInterface;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Mockery;
use Module\UnitTest;

class ConditionTest extends UnitTest
{
    protected function _before(): void
    {
        parent::_before();
    }

    protected function _after(): void
    {
        parent::_after();
    }

    public function testCanCreateConditionUsingDefaultConditionType(): void
    {
        $this->specify(
            'Can create a condition using the default condition type',
            function () {
                $mock = Mockery::mock(
                    Condition::class,
                    ConditionInterface::class,
                    [
                        $this->_data()['attribute'],
                        $this->_data()['value'],
                        FilterInterface::EQUAL
                    ]
                );

                $mock->allows()->getColumn()->andReturn($this->_data()['attribute']);
                $mock->allows()->getValue()->andReturn($this->_data()['value']);
                $mock->allows()->getOperator()->andReturn(FilterInterface::EQUAL);
                $mock->allows()->getType()->andReturn(ConditionInterface::AND);

                expect($mock->getColumn())->equals($this->_data()['attribute']);
                expect($mock->getOperator())->equals($this->_data()['filterOp']);
                expect($mock->getType())->equals($this->_data()['condAndOp']);
            }
        );
    }

    public function testCanCreateConditionUsingAndConditionType(): void
    {
        $this->specify(
            'Can create a condition using the "AND" condition type',
            function () {
                $mock = Mockery::mock(
                    Condition::class,
                    ConditionInterface::class,
                    [
                        $this->_data()['attribute'],
                        $this->_data()['value'],
                        FilterInterface::EQUAL,
                        ConditionInterface::AND
                    ]
                );

                $mock->allows()->getColumn()->andReturn($this->_data()['attribute']);
                $mock->allows()->getValue()->andReturn($this->_data()['value']);
                $mock->allows()->getOperator()->andReturn(FilterInterface::EQUAL);
                $mock->allows()->getType()->andReturn(ConditionInterface::AND);

                expect($mock->getColumn())->equals($this->_data()['attribute']);
                expect($mock->getOperator())->equals($this->_data()['filterOp']);
                expect($mock->getType())->equals($this->_data()['condAndOp']);
            }
        );
    }

    public function testCanCreateConditionUsingOrConditionType(): void
    {
        $this->specify(
            'Can create a condition using the "OR" condition type',
            function () {
                $mock = Mockery::mock(
                    Condition::class,
                    ConditionInterface::class,
                    [
                        $this->_data()['attribute'],
                        $this->_data()['value'],
                        FilterInterface::EQUAL,
                        ConditionInterface::OR
                    ]
                );

                $mock->allows()->getColumn()->andReturn($this->_data()['attribute']);
                $mock->allows()->getValue()->andReturn($this->_data()['value']);
                $mock->allows()->getOperator()->andReturn(FilterInterface::EQUAL);
                $mock->allows()->getType()->andReturn(ConditionInterface::OR);

                expect($mock->getColumn())->equals($this->_data()['attribute']);
                expect($mock->getOperator())->equals($this->_data()['filterOp']);
                expect($mock->getType())->equals($this->_data()['condOrOp']);
            }
        );
    }

    public function testCanCreateConditionWithInvalidConditionType(): void
    {
        $this->specify(
            'Can create a condition with an invalid condition type',
            function () {
                $operator = FilterInterface::EQUAL;
                $mock = Mockery::mock(
                    Condition::class,
                    ConditionInterface::class,
                    [
                        $this->_data()['attribute'],
                        $this->_data()['value'],
                        FilterInterface::EQUAL,
                        'bla'
                    ]
                );

                $mock->allows()->getColumn()->andReturn($this->_data()['attribute']);
                $mock->allows()->getValue()->andReturn($this->_data()['value']);
                $mock->allows()->getOperator()->andReturn(FilterInterface::EQUAL);
                $mock->allows()->getType()->andReturn(ConditionInterface::AND);

                expect($mock->getColumn())->equals($this->_data()['attribute']);
                expect($mock->getOperator())->equals($this->_data()['filterOp']);
                expect($mock->getType())->equals($this->_data()['condAndOp']);
            }
        );
    }

    /**
     * Return test data
     */
    public function _data(): array
    {
        return [
            'attribute' => 'name',
            'condAndOp' => Condition::AND,
            'condOrOp' => Condition::OR,
            'filterOp' => FilterInterface::EQUAL,
            'value' => 'John Doe',
        ];
    }
}
