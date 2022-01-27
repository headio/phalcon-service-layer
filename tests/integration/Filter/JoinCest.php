<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Filter;

use Headio\Phalcon\ServiceLayer\Filter\Filter;
use Headio\Phalcon\ServiceLayer\Filter\Join;
use Headio\Phalcon\ServiceLayer\Filter\JoinInterface;
use Stub\Domain\Entity\Role;
use Stub\Domain\Entity\User;
use Stub\Domain\Entity\RoleUser;
use Stub\Domain\Repository\User as Repository;
use ArrayIterator;
use IntegrationTester;

class JoinCest
{
    public function canCreateInnerJoin(IntegrationTester $I)
    {
        $I->wantToTest('creating an inner join');
        $args = [
            'entity' => User::class,
            'constraint' => User::class . '.id = ' . RoleUser::class . '.user_id',
            'type' => JOIN::INNER,
            'alias' => null,
        ];
        $join = new Join(
            ...array_values($args)
        );

        expect($join->getEntity())->equals($args['entity']);
        expect($join->getConstraint())->equals($args['constraint']);
        expect($join->getType())->equals($args['type']);
        expect($join->getAlias())->equals($args['alias']);
    }

    public function canCreateLeftJoin(IntegrationTester $I)
    {
        $I->wantToTest('creating a left join');
        $args = [
            'entity' => User::class,
            'constraint' => User::class . '.id = ' . RoleUser::class . '.user_id',
            'type' => JOIN::LEFT,
            'alias' => User::class,
        ];
        $join = new Join(
            ...array_values($args)
        );

        expect($join->getEntity())->equals($args['entity']);
        expect($join->getConstraint())->equals($args['constraint']);
        expect($join->getType())->equals($args['type']);
        expect($join->getAlias())->equals($args['alias']);
    }

    public function canCreateRightJoin(IntegrationTester $I)
    {
        $I->wantToTest('creating a right join');
        $args = [
            'entity' => User::class,
            'constraint' => User::class . '.id = ' . RoleUser::class . '.user_id',
            'type' => JOIN::RIGHT,
            'alias' => User::class,
        ];
        $join = new Join(
            ...array_values($args)
        );

        expect($join->getEntity())->equals($args['entity']);
        expect($join->getConstraint())->equals($args['constraint']);
        expect($join->getType())->equals($args['type']);
        expect($join->getAlias())->equals($args['alias']);
    }

    public function canAppendInnerJoinToFilterCriteria(IntegrationTester $I)
    {
        $I->wantToTest('appending an inner join constraint to the filter criteria');
        $filter = new Filter();
        $args = [
            'entity' => User::class,
            'constraint' => User::class . '.id = ' . RoleUser::class . '.user_id',
            'type' => JOIN::INNER,
            'alias' => User::class,
        ];
        $filter->innerJoin(
            $args['entity'],
            $args['constraint'],
            $args['type'],
            $args['alias'],
        );

        expect($filter->hasJoins())->true();

        expect($filter->getJoins())->isInstanceOf(ArrayIterator::class);
    }

    public function canAppendLeftJoinToFilterCriteria(IntegrationTester $I)
    {
        $I->wantToTest('appending a left join constraint to the filter criteria');
        $filter = new Filter();
        $args = [
            'entity' => User::class,
            'constraint' => User::class . '.id = ' . RoleUser::class . '.user_id',
            'alias' => User::class,
        ];
        $filter->leftJoin(
            $args['entity'],
            $args['constraint'],
            $args['alias'],
        );

        expect($filter->hasJoins())->true();

        expect($filter->getJoins())->isInstanceOf(ArrayIterator::class);

        expect($filter->getJoins()[0]->getType())->equals(JOIN::LEFT);
    }

    public function canAppendRightJoinToFilterCriteria(IntegrationTester $I)
    {
        $I->wantToTest('appending a right join constraint to the filter criteria');
        $filter = new Filter();
        $args = [
            'entity' => User::class,
            'constraint' => User::class . '.id = ' . RoleUser::class . '.user_id',
            'alias' => User::class,
        ];
        $filter->rightJoin(
            $args['entity'],
            $args['constraint'],
            $args['alias'],
        );

        expect($filter->hasJoins())->true();

        expect($filter->getJoins())->isInstanceOf(ArrayIterator::class);

        expect($filter->getJoins()[0]->getType())->equals(JOIN::RIGHT);
    }

    public function canAppendInnerJoinConstraintToQueryCriteria(IntegrationTester $I)
    {
        $I->wantToTest('appending an inner join constraint to the query criteria');
        $filter = new Filter();
        $args = [
            'entity' => RoleUser::class,
        ];
        $filter->innerJoin(
            $args['entity'],
        );

        $repository = new Repository(false);
        $criteria = $repository->createCriteria();
        $repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();
        $phql = $builder->getPhql();

        expect($phql)->equals(
            'SELECT [Stub\Domain\Entity\User].* FROM [Stub\Domain\Entity\User] INNER JOIN [Stub\Domain\Entity\RoleUser]'
        );

        $filter = new Filter();
        $args = [
            'entity' => RoleUser::class,
            'constraint' => RoleUser::class . '.user_id = ' . User::class . '.id',
        ];
        $filter->innerJoin(
            $args['entity'],
            $args['constraint'],
        );

        $repository = new Repository(false);
        $criteria = $repository->createCriteria();
        $repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();
        $phql = $builder->getPhql();

        expect($phql)->equals(
            'SELECT [Stub\Domain\Entity\User].* FROM [Stub\Domain\Entity\User]' .
            ' INNER JOIN [Stub\Domain\Entity\RoleUser] ON Stub\Domain\Entity\RoleUser.user_id = Stub\Domain\Entity\User.id'
        );

        $filter = new Filter();
        $args = [
            'entity' => RoleUser::class,
            'constraint' => 'r.user_id = ' . User::class . '.id',
            'alias' => 'r',
        ];
        $filter->innerJoin(
            $args['entity'],
            $args['constraint'],
            $args['alias'],
        );

        $repository = new Repository(false);
        $criteria = $repository->createCriteria();
        $repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();
        $phql = $builder->getPhql();

        expect($phql)->equals(
            'SELECT [Stub\Domain\Entity\User].* FROM [Stub\Domain\Entity\User]' .
            ' INNER JOIN [Stub\Domain\Entity\RoleUser] AS [r] ON r.user_id = Stub\Domain\Entity\User.id'
        );
    }

    public function canAppendLeftJoinConstraintToQueryCriteria(IntegrationTester $I)
    {
        $I->wantToTest('appending a left join constraint to the query criteria');
        $filter = new Filter();
        $args = [
            'entity' => RoleUser::class,
        ];
        $filter->leftJoin(
            $args['entity'],
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria();
        $repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();

        expect($builder->getPhql())->equals(
            'SELECT [Stub\Domain\Entity\User].* FROM [Stub\Domain\Entity\User] LEFT JOIN [Stub\Domain\Entity\RoleUser]'
        );

        $filter = new Filter();
        $args = [
            'entity' => RoleUser::class,
            'constraint' => RoleUser::class . '.user_id = ' . USER::class . '.id',
        ];
        $filter->leftJoin(
            $args['entity'],
            $args['constraint'],
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria();
        $repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();

        expect($builder->getPhql())->equals(
            'SELECT [Stub\Domain\Entity\User].* FROM [Stub\Domain\Entity\User]' .
            ' LEFT JOIN [Stub\Domain\Entity\RoleUser] ON Stub\Domain\Entity\RoleUser.user_id = Stub\Domain\Entity\User.id'
        );

        $filter = new Filter();
        $args = [
            'entity' => RoleUser::class,
            'constraint' => 'r.user_id = ' . USER::class . '.id',
            'alias' => 'r',
        ];
        $filter->leftJoin(
            $args['entity'],
            $args['constraint'],
            $args['alias'],
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria();
        $repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();

        expect($builder->getPhql())->equals(
            'SELECT [Stub\Domain\Entity\User].* FROM [Stub\Domain\Entity\User]' .
            ' LEFT JOIN [Stub\Domain\Entity\RoleUser] AS [r] ON r.user_id = Stub\Domain\Entity\User.id'
        );
    }

    public function canAppendRightJoinConstraintToQueryCriteria(IntegrationTester $I)
    {
        $I->wantToTest('appending a right join constraint to the query criteria');
        $filter = new Filter();
        $args = [
            'entity' => RoleUser::class,
        ];
        $filter->rightJoin(
            $args['entity'],
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria();
        $repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();

        expect($builder->getPhql())->equals(
            'SELECT [Stub\Domain\Entity\User].* FROM [Stub\Domain\Entity\User] RIGHT JOIN [Stub\Domain\Entity\RoleUser]'
        );

        $filter = new Filter();
        $args = [
            'entity' => RoleUser::class,
            'constraint' => RoleUser::class . '.user_id = ' . USER::class . '.id',
        ];
        $filter->rightJoin(
            $args['entity'],
            $args['constraint'],
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria();
        $repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();

        expect($builder->getPhql())->equals(
            'SELECT [Stub\Domain\Entity\User].* FROM [Stub\Domain\Entity\User]' .
            ' RIGHT JOIN [Stub\Domain\Entity\RoleUser] ON Stub\Domain\Entity\RoleUser.user_id = Stub\Domain\Entity\User.id'
        );

        $filter = new Filter();
        $args = [
            'entity' => RoleUser::class,
            'constraint' => 'r.user_id = ' . USER::class . '.id',
            'alias' => 'r',
        ];
        $filter->rightJoin(
            $args['entity'],
            $args['constraint'],
            $args['alias'],
        );
        $repository = new Repository(false);
        $criteria = $repository->createCriteria();
        $repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();

        expect($builder->getPhql())->equals(
            'SELECT [Stub\Domain\Entity\User].* FROM [Stub\Domain\Entity\User]' .
            ' RIGHT JOIN [Stub\Domain\Entity\RoleUser] AS [r] ON r.user_id = Stub\Domain\Entity\User.id'
        );
    }
}
