<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Unit\Model;

use Headio\Phalcon\ServiceLayer\Model\Criteria;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Stub\Domain\Model\Role;
use Stub\Domain\Model\User;
use Phalcon\Db\Column;
use Mockery;
use Module\UnitTest;

class CriteriaTest extends UnitTest
{
    protected function _before(): void
    {
        parent::_before();
    }

    protected function _after(): void
    {
        Mockery::close();
        parent::_after();
    }

    public function testCanAppendEqualCondition()
    {
        $this->specify(
            'Criteria can append an equality condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('eq')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'label',
                    'value' => 'foo',
                ];
                $m->eq($data['column'], $data['value']);
            }
        );
    }

    public function testCanAppendGreaterThanCondition()
    {
        $this->specify(
            'Criteria can append a greater than comparison condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('gt')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'id',
                    'value' => 10,
                ];
                $m->gt($data['column'], $data['value']);
            }
        );
    }

    public function testCanAppendGreaterThanEqualCondition()
    {
        $this->specify(
            'Criteria can append a greater than comparison condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('gte')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'id',
                    'value' => 10,
                ];
                $m->gte($data['column'], $data['value']);
            }
        );
    }

    public function testCanAppendLessThanCondition()
    {
        $this->specify(
            'Criteria can append a less than comparison condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('lt')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'id',
                    'value' => 10,
                ];
                $m->lt($data['column'], $data['value']);
            }
        );
    }

    public function testCanAppendLessThanEqualCondition()
    {
        $this->specify(
            'Criteria can append a less than or equal comparison comparison condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('lte')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'id',
                    'value' => 10,
                ];
                $m->lte($data['column'], $data['value']);
            }
        );
    }

    public function testCanAppendNotEqualCondition()
    {
        $this->specify(
            'Criteria can append a negation equality condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('notEq')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'label',
                    'value' => 'foo',
                ];
                $m->notEq($data['column'], $data['value']);
            }
        );
    }

    public function testCanAppendInCondition()
    {
        $this->specify(
            'Criteria can append an inclusion comparison condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('in')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'id',
                    'value' => [3, 5, 7, 8, 9],
                ];
                $m->in($data['column'], $data['value']);
            }
        );
    }

    public function testCanAppendNotInCondition()
    {
        $this->specify(
            'Criteria can append a negation inclusion comparison condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('notIn')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'id',
                    'value' => [3, 5, 7, 8, 9],
                ];
                $m->notIn($data['column'], $data['value']);
            }
        );
    }

    public function testCanAppendIsNullCondition()
    {
        $this->specify(
            'Criteria can append a null value condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('isNull')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'name',
                    'condition' => 'name IS NULL',
                ];
                $m->isNull($data['column'], $data['condition']);
            }
        );
    }

    public function testAppendIsNotNullCondition()
    {
        $this->specify(
            'Criteria can append a not null value condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('notNull')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'name',
                    'condition' => 'name IS NOT NULL',
                ];
                $m->notNull($data['column'], $data['condition']);
            }
        );
    }

    public function testAppendLikeCondition()
    {
        $this->specify(
            'Criteria can append a pattern match condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('like')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'label',
                    'value' => 'foo',
                ];
                $m->like($data['column'], $data['value']);
            }
        );
    }

    public function testAppendNotLikeCondition()
    {
        $this->specify(
            'Criteria can append a negation pattern match condition to the query criteria',
            function () {
                $m = Mockery::mock(Criteria::class)
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
                $m
                ->shouldReceive('notLike')
                ->once()
                ->andReturn(new Criteria());
                $m
                ->shouldReceive('addCondition')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('mixed'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                );
                $m
                ->shouldReceive('buildConditionExpression')
                ->with(
                    Mockery::type('string'),
                    Mockery::type('string'),
                    Mockery::type('string'),
                )
                ->andReturn(
                    Mockery::type('string'),
                );
                $data = [
                    'column' => 'label',
                    'value' => 'foo',
                ];
                $m->notLike($data['column'], $data['value']);
            }
        );
    }
}
