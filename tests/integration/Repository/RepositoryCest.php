<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Repository;

use Headio\Phalcon\ServiceLayer\Exception\NotFoundException;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use Stub\Domain\Model\User as Model;
use Stub\Domain\Repository\Role as RoleRepository;
use Stub\Domain\Repository\User as Repository;
use IntegrationTester;

class RepositoryCest
{
    private Repository $repository;

    public function _before(IntegrationTester $I)
    {
        $this->repository = new Repository();
    }

    public function canCreateCriteria(IntegrationTester $I)
    {
        $I->wantToTest('creating an instance of query criteria');

        $criteria = $this->repository->createCriteria();

        $I->assertEquals(Model::class, $criteria->getModelName());
    }

    public function canCreateQueryBuilder(IntegrationTester $I)
    {
        $I->wantToTest('creating an instance of query builder');

        $builder = $this->repository->createBuilder();

        $I->assertInstanceOf(BuilderInterface::class, $builder);
    }

    public function canCreateBuilderFromCriteria(IntegrationTester $I)
    {
        $I->wantToTest('creating a query builder from a query criteria instance');

        $data = $this->data();
        $criteria = $this->repository->createCriteria();
        $criteria = $this->repository
            ->createCriteria()
            ->eq('id', $data['primaryKey'])
        ;
        $builder = $criteria->createBuilder();

        $I->assertInstanceOf(BuilderInterface::class, $builder);
        $I->assertEquals($this->data()['builderPhql'], $builder->getPhql());
    }

    public function canCreateQueryFromBuilder(IntegrationTester $I)
    {
        $I->wantToTest('creating a query from a builder instance');

        $data = $this->data();
        $criteria = $this->repository
            ->createCriteria()
            ->eq('id', $data['primaryKey'])
        ;
        $builder = $criteria->createBuilder();
        $query = $builder->getQuery();

        $I->assertEquals(['ID0' => $data['primaryKey']], $query->getBindParams());
        $I->assertEquals(['ID0' => Column::BIND_PARAM_INT], $query->getBindTypes());
    }

    public function canCreateQueryFromBuilderAndReturnValidPhql(IntegrationTester $I)
    {
        $I->wantToTest('building a query from a query criteria instance and returning the expected phql');

        $data = $this->data();
        $criteria = $this->repository->createCriteria()
            ->gt('id', $data['offsetId'])
            ->groupBy(['name'])
            ->orderBy('id');
        $builder = $criteria->createBuilder($criteria->getParams());
        $query = $builder->getQuery();

        expect($builder->getPhql())->equals($data['queryBuilderPhql']);
        expect($query->getBindParams())->equals(['ID0' => $data['offsetId']]);
        expect($query->getBindTypes())->equals(['ID0' => Column::BIND_PARAM_INT]);
    }

    public function canGetModelName(IntegrationTester $I)
    {
        $I->wantToTest('returning the model managed by the repository');

        $result = $this->repository->getModel();

        $I->assertEquals(Model::class, $result);
    }

    public function canFetchNumberOfRecords(IntegrationTester $I)
    {
        $I->wantToTest('fetching number of records');

        $data = $this->data();
        $criteria = $this->repository
            ->createCriteria()
            ->like('email', $data['like']['email'])
            ->columns(['c' => 'COUNT(*)'])
        ;
        $result = $this->repository->fetchColumn($criteria);

        $I->assertTrue(is_int($result));
    }

    public function canFindFirstByProperty(IntegrationTester $I)
    {
        $I->wantToTest('fetching the first record using a model property');

        $data = $this->data();
        $result = $this->repository->findFirstByEmail(
            $data['email']
        );

        $I->assertInstanceOf('Phalcon\\Mvc\\Model', $result);
        $I->assertInstanceOf(ModelInterface::class, $result);
    }

    public function canFindByPk(IntegrationTester $I)
    {
        $I->wantToTest('fetching a record by primary key');

        $data = $this->data();
        $result = $this->repository->findByPk($data['primaryKey']);

        $I->assertInstanceOf('Phalcon\\Mvc\\Model', $result);
        $I->assertInstanceOf(ModelInterface::class, $result);
        $I->assertInstanceOf(Model::class, $result);
    }

    public function canValidateFindByPkThrows404(IntegrationTester $I)
    {
        $I->wantToTest('fetching a record using an unknown primary key');
        $I->expectThrowable(
            new NotFoundException('404 Not Found'),
            function () {
                $result = $this->repository->findByPk(-1000000);
            }
        );
    }

    public function canFindAll(IntegrationTester $I)
    {
        $I->wantToTest('fetching records using find with criteria');

        $data = $this->data();
        $criteria = $this->repository
            ->createCriteria()
            ->like('email', $data['like']['email'])
        ;
        $result = $this->repository->find($criteria);

        $I->assertInstanceOf(Simple::class, $result);
    }

    public function canFetchRelatedModels(IntegrationTester $I)
    {
        $I->wantToTest('fetching the related models for a relationship alias definition');

        $data = $this->data();
        $model = $this->repository->findByPk($data['primaryKey']);
        $alias = $data['alias'];
        $result = $this->repository->{"get$alias"}(
            $model,
        );

        $I->assertInstanceOf(ResultsetInterface::class, $result);
    }

    public function canFetchNumberOfRelatedModelRecords(IntegrationTester $I)
    {
        $I->wantToTest('fetching the number of related model records for a relationship alias definition');

        $data = $this->data();
        $model = $this->repository->findByPk($data['primaryKey']);
        $alias = $data['alias'];
        $result = $this->repository->{"count$alias"}(
            $model,
        );
        $I->debug($result);
        $I->assertTrue(is_int($result));
    }

    private function data(): array
    {
        return [
            'alias' => 'roles',
            'builderPhql' => 'SELECT [Stub\\Domain\\Model\\User].* FROM [Stub\\Domain\\Model\\User] WHERE id = :ID0:',
            'email' => 'john.doe@headcrumbs.io',
            'like' => [
                'email' => 'headcrumbs',
            ],
            'offsetId' => 10,
            'primaryKey' => 1,
            'queryBuilderPhql' => 'SELECT [Stub\Domain\Model\User].* FROM [Stub\Domain\Model\User] WHERE id > :ID0: GROUP BY [name] ORDER BY id',
        ];
    }
}
