<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Model;

use Headio\Phalcon\ServiceLayer\Model\Criteria;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Stub\Domain\Model\Role;
use Stub\Domain\Model\User;
use Phalcon\Db\Column;
use Phalcon\Di\DiInterface;
use IntegrationTester;

class CriteriaCest
{
    public function canAppendEqualCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending an equality condition to the query criteria');

        $data = [
            'column' => 'label',
            'value' => 'foo',
            'bind' => ['LABEL0' => 'foo'],
            'bindTypes' => ['LABEL0' => Column::BIND_PARAM_STR],
            'conditions' => 'label = :LABEL0:',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->eq($data['column'], $data['value']);

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendGreaterThanCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending a greater than comparison condition to the query criteria');

        $data = [
            'column' => 'id',
            'value' => 10,
            'bind' => ['ID0' => 10],
            'bindTypes' => ['ID0' => Column::BIND_PARAM_INT],
            'conditions' => 'id > :ID0:',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->gt($data['column'], $data['value']);

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendGreaterThanEqualCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending a great than or equal comparison condition to the query criteria');

        $data = [
            'column' => 'id',
            'value' => 10,
            'bind' => ['ID0' => 10],
            'bindTypes' => ['ID0' => Column::BIND_PARAM_INT],
            'conditions' => 'id >= :ID0:',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->gte($data['column'],$data['value']);

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendLessThanCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending a less than comparison condition to the query criteria');

        $data = [
            'column' => 'id',
            'value' => 10,
            'bind' => ['ID0' => 10],
            'bindTypes' => ['ID0' => Column::BIND_PARAM_INT],
            'conditions' => 'id < :ID0:',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->lt($data['column'], $data['value']);

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendLessThanEqualCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending a less than or equal comparison condition to the query criteria');

        $data = [
            'column' => 'id',
            'value' => 10,
            'bind' => ['ID0' => 10],
            'bindTypes' => ['ID0' => Column::BIND_PARAM_INT],
            'conditions' => 'id <= :ID0:',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->lte($data['column'], $data['value']);

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendNotEqualCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending a negation equality condition to the query criteria');

        $data = [
            'column' => 'label',
            'value' => 'foo',
            'bind' => ['LABEL0' => 'foo'],
            'bindTypes' => ['LABEL0' => Column::BIND_PARAM_STR],
            'conditions' => 'label <> :LABEL0:',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->notEq($data['column'], $data['value']);

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendInCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending an inclusion comparison condition to the query criteria');

        $data = [
            'column' => 'id',
            'value' => [3, 5, 7, 8, 9],
            'bind' => ['ID0' => [3, 5, 7, 8, 9]],
            'bindTypes' => ['ID0' => Column::BIND_PARAM_INT],
            'conditions' => 'id IN ({ID0:array})',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->in($data['column'], $data['value']);

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendNotInCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending a negation inclusion comparison condition to the query criteria');

        $data = [
            'column' => 'id',
            'value' => [3, 5, 7, 8, 9],
            'bind' => ['ID0' => [3, 5, 7, 8, 9]],
            'bindTypes' => ['ID0' => Column::BIND_PARAM_INT],
            'conditions' => 'id NOT IN ({ID0:array})',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->notIn($data['column'], $data['value']);

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendIsNullCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending a null value condition to the query criteria');

        $data = [
            'column' => 'name',
            'conditions' => 'name IS NULL',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(User::class)
        ->isNull($data['column']);

        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendIsNotNullCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending a not null value condition to the query criteria');

        $data = [
            'column' => 'name',
            'conditions' => 'name IS NOT NULL',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(User::class)
        ->isNotNull($data['column']);

        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendLikeCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending a pattern match condition to the query criteria');

        $data = [
            'column' => 'label',
            'value' => 'foo',
            'bind' => ['LABEL0' => '%foo%'],
            'bindTypes' => ['LABEL0' => Column::BIND_PARAM_STR],
            'conditions' => 'label LIKE LOWER(:LABEL0:)',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->like($data['column'], $data['value']);

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendNotLikeCondition(IntegrationTester $I)
    {
        $I->wantToTest('appending a negation pattern match condition to the query criteria');

        $data = [
            'column' => 'label',
            'value' => 'foo',
            'bind' => ['LABEL0' => '%foo%'],
            'bindTypes' => ['LABEL0' => Column::BIND_PARAM_STR],
            'conditions' => 'label NOT LIKE LOWER(:LABEL0:)',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->notLike($data['column'], $data['value']);

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
    }

    public function canAppendMultipleConditions(IntegrationTester $I)
    {
        $I->wantToTest('appending multiple conditions to the query criteria');

        $data = [
            'eq' => [
                'column' => 'label',
                'value' => 'foo',
            ],
            'in' => [
                'column' => 'id',
                'values' => [3, 5, 7, 8, 9],
            ],
            'bind' => [
                'LABEL0' => 'foo',
                'ID1' => [3, 5, 7, 8, 9],
            ],
            'bindTypes' => [
                'LABEL0' => Column::BIND_PARAM_STR,
                'ID1' => Column::BIND_PARAM_INT,
            ],
            'conditions' => '(label = :LABEL0:) AND (id IN ({ID1:array}))',
            'phql' => 'SELECT [Stub\Domain\Model\Role].* FROM [Stub\Domain\Model\Role] WHERE' .
                           ' (label = :LABEL0:) AND (id IN ({ID1:array})) ORDER BY label LIMIT :APL0:',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->eq(
            $data['eq']['column'],
            $data['eq']['value']
        )
        ->in(
            $data['in']['column'],
            $data['in']['values']
        )
        ->orderBy($data['eq']['column'])
        ->limit(50);

        $phql = $criteria->createBuilder()->getPhql();

        $I->assertEquals($data['bind'], $criteria->getParams()['bind']);
        $I->assertEquals($data['bindTypes'], $criteria->getParams()['bindTypes']);
        $I->assertEquals($data['conditions'], $criteria->getConditions());
        $I->assertEquals($data['phql'], $phql);
    }

    public function canReturnCacheParams(IntegrationTester $I)
    {
        $I->wantToTest('fetching the parameters required to generate a cache key');

        $data = [
            'eq' => [
                'column' => 'label',
                'value' => 'foo',
            ],
            'in' => [
                'column' => 'id',
                'values' => [3, 5, 7, 8, 9],
            ],
            'bind' => [
                'LABEL0' => 'foo',
                'ID1' => [3, 5, 7, 8, 9],
            ],
            'bindTypes' => [
                'LABEL0' => Column::BIND_PARAM_STR,
                'ID1' => Column::BIND_PARAM_INT,
            ],
            'conditions' => '(label = :LABEL0:) AND (id IN ({ID1:array}))',
        ];
        $criteria = new Criteria();
        $criteria->setDI(
            $I->getApplication()->getDI()
        );
        $criteria
        ->setModelName(Role::class)
        ->eq(
            $data['eq']['column'],
            $data['eq']['value']
        )
        ->in(
            $data['in']['column'],
            $data['in']['values']
        )
        ->orderBy($data['eq']['column'])
        ->limit(50);

        $result = $criteria->getCacheParams();

        $I->assertArrayNotHasKey('di', $result);
        $I->assertArrayNotHasKey('bindTypes', $result);
        $I->assertArrayHasKey('conditions', $result);
        $I->assertArrayHasKey('bind', $result);
    }
}
