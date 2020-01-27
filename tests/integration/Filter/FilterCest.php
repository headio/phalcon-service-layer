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

namespace Integration\Filter;

use Headio\Phalcon\ServiceLayer\Filter\{ ConditionInterface, FilterInterface };
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Criteria;
use Stub\Domain\Filter\Role as Filter;
use Stub\Domain\Repository\Role as Repository;
use ArrayIterator;
use IntegrationTester;

class FilterCest
{
    public function _before(IntegrationTester $I)
    {
    }

    public function canAppendCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a condition to the filter criteria');

        $filter = new Filter();
        $filter->addCondition(
            $this->_data()['cond']['col'],
            $this->_data()['cond']['val'],
            $this->_data()['cond']['condOp'],
            $this->_data()['cond']['filterOp']
        );

        expect($filter->hasConditions())->true();

        expect($filter->getConditions())->isInstanceOf(ArrayIterator::class);
    }

    public function canClearConditions(IntegrationTester $I)
    {
        $I->wantToTest('Clearing all conditions assigned to the filter criteria');

        $filter = new Filter();
        $filter->clearConditions();

        expect(empty($filter->hasConditions()))->true();
    }

    public function canAppendAGreaterThanCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a greater than comparison condition to the filter criteria');

        $filter = new Filter();
        $filter->gt(
            $this->_data()['gt']['column'],
            $this->_data()['gt']['value']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $bind = $criteria->getParams()['bind'];
        $bindTypes = $criteria->getParams()['bindTypes'];

        expect($conditions)->equals($this->_data()['gtCond']);
        expect($bind)->equals($this->_data()['gtBind']);
        expect($bindTypes)->equals($this->_data()['gtBindType']);
    }

    public function CanAppendAGreaterThanEqualCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a great than or equal comparison condition to the filter criteria');

        $filter = new Filter();
        $filter->gte(
            $this->_data()['gte']['column'],
            $this->_data()['gte']['value']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $bind = $criteria->getParams()['bind'];
        $bindTypes = $criteria->getParams()['bindTypes'];

        expect($conditions)->equals($this->_data()['gteCond']);
        expect($bind)->equals($this->_data()['gteBind']);
        expect($bindTypes)->equals($this->_data()['gteBindType']);
    }

    public function canAppendALessThanCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a less than comparison condition to the filter criteria');

        $filter = new Filter();
        $filter->lt(
            $this->_data()['lt']['column'],
            $this->_data()['lt']['value']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $bind = $criteria->getParams()['bind'];
        $bindTypes = $criteria->getParams()['bindTypes'];

        expect($conditions)->equals($this->_data()['ltCond']);
        expect($bind)->equals($this->_data()['ltBind']);
        expect($bindTypes)->equals($this->_data()['ltBindType']);
    }

    public function CanAppendALessThanEqualCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a less than or equal comparison condition to the filter criteria');

        $filter = new Filter();
        $filter->lte(
            $this->_data()['lte']['column'],
            $this->_data()['lte']['value']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $bind = $criteria->getParams()['bind'];
        $bindTypes = $criteria->getParams()['bindTypes'];

        expect($conditions)->equals($this->_data()['lteCond']);
        expect($bind)->equals($this->_data()['lteBind']);
        expect($bindTypes)->equals($this->_data()['lteBindType']);
    }

    public function canAppendAnEqualCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending an equality condition to the filter criteria');

        $filter = new Filter();
        $filter->eq(
            $this->_data()['eq']['column'],
            $this->_data()['eq']['value']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $bind = $criteria->getParams()['bind'];
        $bindTypes = $criteria->getParams()['bindTypes'];

        expect($conditions)->equals($this->_data()['eqCond']);
        expect($bind)->equals($this->_data()['eqBind']);
        expect($bindTypes)->equals($this->_data()['eqBindType']);
    }

    public function canAppendANotEqualCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a nagation equality condition to the filter criteria');

        $filter = new Filter();
        $filter->notEq(
            $this->_data()['eq']['column'],
            $this->_data()['eq']['value']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $bind = $criteria->getParams()['bind'];
        $bindTypes = $criteria->getParams()['bindTypes'];

        expect($conditions)->equals($this->_data()['notEqCond']);
        expect($bind)->equals($this->_data()['notEqBind']);
        expect($bindTypes)->equals($this->_data()['notEqBindType']);
    }

    public function canAppendAnInCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending an inclusion comparison condition to the filter criteria');

        $filter = new Filter();
        $filter->in(
            $this->_data()['in']['column'],
            $this->_data()['in']['value']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $bind = $criteria->getParams()['bind'];
        $bindTypes = $criteria->getParams()['bindTypes'];

        expect($conditions)->equals($this->_data()['inCond']);
        expect($bind)->equals($this->_data()['inBind']);
        expect($bindTypes)->equals($this->_data()['inBindType']);
    }

    public function canAppendANotInCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a negation inclusion comparison condition to the filter criteria'); 

        $filter = new Filter();
        $filter->notIn(
            $this->_data()['in']['column'],
            $this->_data()['in']['value']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $bind = $criteria->getParams()['bind'];
        $bindTypes = $criteria->getParams()['bindTypes'];

        expect($conditions)->equals($this->_data()['notInCond']);
        expect($bind)->equals($this->_data()['notInBind']);
        expect($bindTypes)->equals($this->_data()['notInBindType']);
    }

    public function canAppendIsNullCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a null value condition to the filter criteria');

        $filter = new Filter();
        $filter->isNull(
            $this->_data()['isNull']['column']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();

        expect($conditions)->equals($this->_data()['isNullCond']);
    }

    public function canAppendIsNotNullCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a not null value condition to the filter criteria'); 

        $filter = new Filter();
        $filter->isNotNull(
            $this->_data()['isNotNull']['column']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();

        expect($conditions)->equals($this->_data()['isNotNullCond']);
    }

    public function canAppendALikeCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a pattern match condition to the filter criteria');

        $filter = new Filter();
        $filter->like(
            $this->_data()['like']['column'],
            $this->_data()['like']['value']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $bind = $criteria->getParams()['bind'];
        $bindTypes = $criteria->getParams()['bindTypes'];

        expect($conditions)->equals($this->_data()['likeCond']);
        expect($bind)->equals($this->_data()['likeBind']);
        expect($bindTypes)->equals($this->_data()['likeBindType']);
    }

    public function canAppendANotLikeCondition(IntegrationTester $I)
    {
        $I->wantToTest('Appending a negation pattern match condition to the filter criteria');

        $filter = new Filter();
        $filter->notLike(
            $this->_data()['like']['column'],
            $this->_data()['like']['value']
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $bind = $criteria->getParams()['bind'];
        $bindTypes = $criteria->getParams()['bindTypes'];

        expect($conditions)->equals($this->_data()['notLikeCond']);
        expect($bind)->equals($this->_data()['notLikeBind']);
        expect($bindTypes)->equals($this->_data()['notLikeBindType']);
    }

    public function canAppendMultipleConditions(IntegrationTester $I)
    {
        $I->wantToTest('Appending multiple conditions to the filter criteria');

        $filter = new Filter();
        $filter->eq(
            $this->_data()['eq']['column'],
            $this->_data()['eq']['value']
        )
        ->in(
            $this->_data()['in']['column'],
            $this->_data()['in']['value']
        )
        ->orderBy($this->_data()['eq']['column'])
        ->limit(50);

        $repository = new Repository(false);
        $criteria = $repository->createCriteria(); 
        $repository->applyFilter($criteria, $filter);
        $conditions = $criteria->getConditions();
        $phql = $criteria->createBuilder()->getPhql();

        expect($conditions)->equals($this->_data()['multiCond']);
        expect($phql)->equals($this->_data()['multiPhql']);
    }
    /**
     * Return test data
     */
    public function _data() : array
    {
        return [
            'cond' => [
                'col' => 'label',
                'val' => 'foo',
                'condOp' => ConditionInterface::AND,
                'filterOp' => FilterInterface::EQUAL
            ],
            'eq' => ['column' => 'label', 'value' => 'foo'],
            'eqBind' => ['LABEL0' => 'foo'],
            'eqBindType' => ['LABEL0' => Column::BIND_PARAM_STR],
            'eqCond' => 'label = :LABEL0:',
            'gt' => ['column' => 'id', 'value' => 10],
            'gtBind' => ['ID0' => 10],
            'gtBindType' => ['ID0' => Column::BIND_PARAM_INT],
            'gtCond' => 'id > :ID0:',
            'gte' => ['column' => 'id', 'value' => 10],
            'gteBind' => ['ID0' => 10],
            'gteBindType' => ['ID0' => Column::BIND_PARAM_INT],
            'gteCond' => 'id >= :ID0:',
            'in' => ['column' => 'id', 'value' => [3,5,7,8,9]],
            'inBind' => ['ID0' => [3,5,7,8,9]],
            'inBindType' => ['ID0' => Column::BIND_PARAM_INT],
            'inCond' => 'id IN ({ID0:array})',
            'isNull' => ['column' => 'name'],
            'isNullCond' => 'name IS NULL',
            'isNotNull' => ['column' => 'name'],
            'isNotNullCond' => 'name IS NOT NULL',
            'notInBind' => ['ID0' => [3,5,7,8,9]],
            'notInBindType' => ['ID0' => Column::BIND_PARAM_INT],
            'notInCond' => 'id NOT IN ({ID0:array})',
            'lt' => ['column' => 'id', 'value' => 10],
            'ltBind' => ['ID0' => 10],
            'ltBindType' => ['ID0' => Column::BIND_PARAM_INT],
            'ltCond' => 'id < :ID0:',
            'lte' => ['column' => 'id', 'value' => 10],
            'lteBind' => ['ID0' => 10],
            'lteBindType' => ['ID0' => Column::BIND_PARAM_INT],
            'lteCond' => 'id <= :ID0:',
            'like' => ['column' => 'label','value' => 'foo'],
            'likeBind' => ['LABEL0' => '%foo%'],
            'likeBindType' => ['LABEL0' => Column::BIND_PARAM_STR],
            'likeCond' => 'label LIKE LOWER(:LABEL0:)',
            'multiCond' => '(label = :LABEL0:) AND (id IN ({ID1:array}))',
            'multiPhql' => 'SELECT [Stub\Domain\Entity\Role].* FROM [Stub\Domain\Entity\Role] WHERE' . 
                           ' (label = :LABEL0:) AND (id IN ({ID1:array})) ORDER BY label LIMIT :APL0:',
            'notLikeBind' => ['LABEL0' => '%foo%'],
            'notLikeBindType' => ['LABEL0' => Column::BIND_PARAM_STR],
            'notLikeCond' => 'label NOT LIKE LOWER(:LABEL0:)',
            'notEqBind' => ['LABEL0' => 'foo'],
            'notEqBindType' => ['LABEL0' => Column::BIND_PARAM_STR],
            'notEqCond' => 'label <> :LABEL0:',
        ];
    }
}
