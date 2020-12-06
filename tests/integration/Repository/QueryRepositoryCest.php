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

namespace Integration\Repository;

use Headio\Phalcon\ServiceLayer\Entity\AbstractEntity;
use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Headio\Phalcon\ServiceLayer\Exception\NotFoundException;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\CriteriaInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use Stub\Domain\Entity\User as Entity;
use Stub\Domain\Filter\User as Filter;
use Stub\Domain\Repository\User as Repository;
use IntegrationTester;

class QueryRepositoryCest
{
    private $repository;

    public function _before(IntegrationTester $I)
    {
        $this->repository = new Repository($this->_data()['cache']);
        $this->di = $I->getApplication()->getDI();
    }

    public function canReturnRecordCount(IntegrationTester $I)
    {
        $I->wantToTest('Query repository can return number of results for filter');

        $filter = $this->repository->getQueryFilter();
        $result = $this->repository->count($filter);

        expect_that(is_int($result));
    }

    public function canCreateCriteria(IntegrationTester $I)
    {
        $I->wantToTest(
            'Query repository returns an instance of the query criteria pre-populated ' .
            'with the entity managed by the repository.'
        );

        $criteria = $this->repository->createCriteria();

        expect($criteria->getModelName())->equals(Entity::class);
    }

    public function canCreateQuery(IntegrationTester $I)
    {
        $I->wantToTest('Query repository can return a phalcon builder instance');

        $builder = $this->repository->createQuery();

        expect($builder)->isInstanceOf(BuilderInterface::class);
        expect($builder)->isInstanceOf(Builder::class);
    }

    public function canApplyFilterToQueryCriteria(IntegrationTester $I)
    {
        $I->wantTo('Apply a filter to the query criteria');
        $criteria = $this->repository->createCriteria();
        $filter = $this->repository->getQueryFilter()->setPrimaryKey($this->_data()['primaryKey']);
        $this->repository->applyFilter($criteria, $filter);

        $I->assertEquals($criteria->getConditions(), $this->_data()['primaryKeyCondition']);
        $I->assertArrayHasKey('bind', $criteria->getParams());
        $I->assertArrayHasKey('bindTypes', $criteria->getParams());

        return $criteria;
    }

    public function canCreateBuilderFromCriteria(IntegrationTester $I)
    {
        $I->wantToTest('Query repository can create query builder from criteria');

        $criteria = $this->repository->createCriteria();
        $filter = $this->repository->getQueryFilter()->setPrimaryKey($this->_data()['primaryKey']);
        $this->repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();

        $I->assertInstanceOf(BuilderInterface::class, $builder);
        $I->assertEquals($this->_data()['builderPhql'], $builder->getPhql());
    }

    public function canCreateQueryFromBuilder(IntegrationTester $I)
    {
        $I->wantToTest(
            'Query repository can apply filter to query criteria, create query from builder ' .
            'and return exprected bind types'
        );

        $criteria = $this->repository->createCriteria();
        $filter = $this->repository->getQueryFilter()->setPrimaryKey($this->_data()['primaryKey']);
        $this->repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder();
        $query = $builder->getQuery();

        expect($query->getBindParams())->equals(['ID0' => $this->_data()['primaryKey']]);
        expect($query->getBindTypes())->equals(['ID0' => Column::BIND_PARAM_INT]);
    }

    public function canCreateQueryFromBuilderAndReturnValidPhql(IntegrationTester $I)
    {
        $I->wantToTest(
            'Query repository can apply filter to query criteria, create query from builder ' .
            'and return expected phql syntax and bind types'
        );

        $filter = $this->repository->getQueryFilter()
            ->offset($this->_data()['offsetId'])
            ->groupBy(['label'])
            ->orderBy('id', 'ASC');
        $criteria = $this->repository->createCriteria();
        $this->repository->applyFilter($criteria, $filter);
        $builder = $criteria->createBuilder($criteria->getParams());
        $query = $builder->getQuery();

        expect($builder->getPhql())->equals($this->_data()['queryBuilderPhql']);
        expect($query->getBindParams())->equals(['ID0' => $this->_data()['offsetId']]);
        expect($query->getBindTypes())->equals(['ID0' => Column::BIND_PARAM_INT]);
    }

    public function canFind(IntegrationTester $I)
    {
        $I->wantToTest('Query repository can fetch records using filter interface');

        $filter = $this->repository->getQueryFilter();
        $result = $this->repository->find($filter);

        expect($result)->isInstanceOf(Simple::class);
    }

    public function canFindByPk(IntegrationTester $I)
    {
        $I->wantToTest('Query repository can fetch record by primary key');

        $result = $this->repository->findByPk(1);

        expect($result)->isInstanceOf('Phalcon\\Mvc\\Model');
        expect($result)->isInstanceOf(EntityInterface::class);
        expect($result)->isInstanceOf(AbstractEntity::class);
    }

    public function canFindByPkThrows404(IntegrationTester $I)
    {
        $I->wantToTest('Query repository cannot fetch record by primary key with invalid parameter');
        $I->expectThrowable(
            new NotFoundException('404 Not Found'),
            function () {
                $result = $this->repository->findByPk(-1000000);
            }
        );
    }

    public function canFindFirstByUrl(IntegrationTester $I)
    {
        $I->wantToTest('Query repository can fetch first record by valid property name');

        $result = $this->repository->findFirstByEmail($this->_data()['email']);

        expect($result)->isInstanceOf('Phalcon\\Mvc\\Model');
        expect($result)->isInstanceOf(EntityInterface::class);
        expect($result)->isInstanceOf(AbstractEntity::class);
    }

    public function canGetEntity(IntegrationTester $I)
    {
        $I->wantToTest('Query repository can return the entity managed by the repository');

        $result = $this->repository->getEntity();

        expect($result)->equals(Entity::class);
    }

    public function canGetQueryFilter(IntegrationTester $I)
    {
        $I->wantToTest('Query repository can return the query filter assigned to the repository');

        $result = $this->repository->getQueryFilter();

        expect($result)->isInstanceOf(Filter::class);
        expect($result)->isInstanceOf(FilterInterface::class);
    }

    public function canGetRelated(IntegrationTester $I)
    {
        $I->wantToTest('Query repository can return the related models');

        $filter = $this->repository->getQueryFilter();
        $entityName = $this->repository->getEntity();
        $entity = new $entityName();

        $result = $this->repository->getRelated(
            $this->_data()['alias'],
            $entity,
            $filter
        );

        expect($result)->isInstanceOf(ResultsetInterface::class);
    }

    public function canGetUnrelated(IntegrationTester $I)
    {
        $I->wantToTest('Query repository can return the unrelated models');

        $entityName = $this->repository->getEntity();
        $entity = new $entityName();
        $metaData = $this->di->get('modelsMetadata');
        $result = $this->repository->getUnrelated(
            new Simple($metaData->getColumnMap($entity), $entity, null),
            new Filter()
        );

        expect($result)->isInstanceOf(ResultsetInterface::class);
    }

    /**
     * Return test data
     */
    public function _data() : array
    {
        return [
            'alias' => 'roles',
            'builderPhql' => 'SELECT [Stub\\Domain\\Entity\\User].* FROM [Stub\\Domain\\Entity\\User] WHERE id = :ID0:',
            'cache' => false,
            'email' => 'john.doe@headcrumbs.io',
            'keyword' => 'admin.resource.read',
            'keywordCondition' => 'label LIKE LOWER(:LABEL0:)',
            'ids' => [2, 3, 4, 5, 6],
            'offsetId' => 25,
            'offsetIdCondition' => 'id > :ID0:',
            'primaryKey' => 10,
            'primaryKeyCondition' => 'id = :ID0:',
            'queryBuilderPhql' => 'SELECT [Stub\Domain\Entity\User].* FROM [Stub\Domain\Entity\User] WHERE id > :ID0: GROUP BY [label] ORDER BY id'
        ];
    }
}
