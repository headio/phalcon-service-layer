<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Repository;

use Headio\Phalcon\ServiceLayer\Cache\Manager as cacheManager;
use Headio\Phalcon\ServiceLayer\Listener\CacheListener;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Stub\Domain\Model\User as Model;
use Stub\Domain\Repository\CacheableUser as Repository;
use Stub\Domain\Repository\Role as RoleRepository;
use Phalcon\Db\Column;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use IntegrationTester;
use function current;
use function is_int;

class CacheableRepositoryCest
{
    private cacheManager $cacheManager;

    private Repository $repository;

    public function _before(IntegrationTester $I)
    {
        $eventsManager = new EventsManager();
        $repository = new Repository();
        $repository->setEventsManager(
            $eventsManager,
        );
        $cacheManager = $I->getService('cacheManager');
        $eventsManager->attach(
            'cache',
            new CacheListener(
                $cacheManager
            )
        );
        $this->repository = $repository;
        $this->cacheManager = $cacheManager;
        $prefix = $this->cacheManager->getCache()->getAdapter()->getPrefix();

        $I->assertTrue(
            $this->cacheManager->getCache()->getAdapter()->clear(
                $prefix,
            )
        );
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

        $criteria = $this->repository
            ->createCriteria()
            ->eq('id', $this->data()['primaryKey'])
        ;
        $builder = $criteria->createBuilder();
        $query = $builder->getQuery();

        $I->assertEquals(['ID0' => $this->data()['primaryKey']], $query->getBindParams());
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

    public function canCacheFetchRecordCount(IntegrationTester $I)
    {
        $I->wantToTest('caching fetch record count');

        $criteria = $this->repository
            ->createCriteria()
            ->columns(['c' => 'COUNT(*)'])
        ;
        $result = $this->repository->fetchColumn($criteria);
        // generate the key to fetch
        // and validate the data from cache
        $query = $criteria
            ->createBuilder()
            ->getQuery()
        ;
        $key = $this->cacheManager->generateKey(
            $this->repository->getModel(),
            $query,
        );
        $resultset = $this->cacheManager->getCache()->get($key);

        $I->assertEquals(
            $result,
            current($resultset->toArray())['c']
        );
    }

    public function canOmitCacheFetchRecordCount(IntegrationTester $I)
    {
        $I->wantToTest('omitting the cache on a fetch record count');

        $criteria = $this->repository
            ->createCriteria()
            ->columns(['c' => 'COUNT(*)'])
        ;
        $result = $this->repository->nocache()->fetchColumn($criteria);
        // generate the key to fetch
        // and validate the data from cache
        $query = $criteria
            ->createBuilder()
            ->getQuery()
        ;
        $key = $this->cacheManager->generateKey(
            $this->repository->getModel(),
            $query,
        );

        // no key present since cache omitted
        $I->assertFalse(
            $this->cacheManager->getCache()->has($key)
        );

        // executing a second request should use cache
        $result = $this->repository->fetchColumn($criteria);

        $I->assertTrue(
            $this->cacheManager->getCache()->has($key)
        );

        $resultset = $this->cacheManager->getCache()->get($key);

        $I->assertEquals(
            $result,
            current($resultset->toArray())['c']
        );
    }

    public function canCacheFindFirstByProperty(IntegrationTester $I)
    {
        $I->wantToTest('caching fetch first record by property');

        $data = $this->data();
        $result = $this->repository->findFirstByEmail(
            $data['email'],
        );
        // generate the key to fetch
        // and validate the data from cache
        $query = $this->repository
            ->createCriteria()
            ->eq('email', $data['email'])
            ->createBuilder()
            ->getQuery()
            ->setUniqueRow(true)
        ;
        $key = $this->cacheManager->generateKey(
            $this->repository->getModel(),
            $query,
        );
        $resultset = $this->cacheManager->getCache()->get($key);

        $I->assertInstanceOf(
            Model::class,
            $resultset->getFirst(),
        );
    }

    public function canOmitCacheFindFirstByProperty(IntegrationTester $I)
    {
        $I->wantToTest('omitting the cache on a fetch first record by property');

        $data = $this->data();
        $result = $this->repository->nocache()->findFirstByEmail(
            $data['email'],
        );
        // generate the key to fetch
        // and validate the data from cache
        $query = $this->repository
            ->createCriteria()
            ->eq('email', $data['email'])
            ->createBuilder()
            ->getQuery()
            ->setUniqueRow(true)
        ;
        $key = $this->cacheManager->generateKey(
            $this->repository->getModel(),
            $query,
        );

        // no key present since cache omitted
        $I->assertFalse(
            $this->cacheManager->getCache()->has($key)
        );

        // executing a second request should use cache
        $result = $this->repository->findFirstByEmail(
            $data['email'],
        );

        $I->assertTrue(
            $this->cacheManager->getCache()->has($key)
        );

        $resultset = $this->cacheManager->getCache()->get($key);

        $I->assertInstanceOf(
            Model::class,
            $resultset->getFirst(),
        );
    }

    public function canCacheFindByPk(IntegrationTester $I)
    {
        $I->wantToTest('caching fetch by primary key');

        $data = $this->data();
        $result = $this->repository->findByPk($data['primaryKey']);

        // generate the key to fetch
        // and validate the data from cache
        $query = $this->repository
            ->createCriteria()
            ->eq('id', $data['primaryKey'])
            ->createBuilder()
            ->getQuery()
            ->setUniqueRow(true)
        ;
        $key = $this->cacheManager->generateKey(
            $this->repository->getModel(),
            $query,
        );
        $resultset = $this->cacheManager->getCache()->get($key);

        $I->assertInstanceOf(
            Model::class,
            $resultset->getFirst(),
        );
    }

    public function canOmitCacheFindByPk(IntegrationTester $I)
    {
        $I->wantToTest('caching fetch by primary key');

        $data = $this->data();
        $result = $this->repository->nocache()->findByPk($data['primaryKey']);

        // generate the key to fetch
        // and validate the data from cache
        $query = $this->repository
            ->createCriteria()
            ->eq('id', $data['primaryKey'])
            ->createBuilder()
            ->getQuery()
            ->setUniqueRow(true)
        ;
        $key = $this->cacheManager->generateKey(
            $this->repository->getModel(),
            $query,
        );

        // no key present since cache omitted
        $I->assertFalse(
            $this->cacheManager->getCache()->has($key)
        );

        // executing a second request should use cache
        $result = $this->repository->findByPk($data['primaryKey']);

        $I->assertTrue(
            $this->cacheManager->getCache()->has($key)
        );
        
        $resultset = $this->cacheManager->getCache()->get($key);

        $I->assertInstanceOf(
            Model::class,
            $resultset->getFirst(),
        );
    }

    public function canCacheFindAll(IntegrationTester $I)
    {
        $I->wantToTest('caching fetch all by query criteria');

        $data = $this->data();
        $criteria = $this->repository
            ->createCriteria()
            ->like('email', $data['like']['email'])
        ;
        $result = $this->repository->find($criteria);
        // generate the key to fetch
        // and validate the data from cache
        $query = $criteria
            ->createBuilder()
            ->getQuery()
        ;
        $key = $this->cacheManager->generateKey(
            $this->repository->getModel(),
            $query,
        );

        $resultset = $this->cacheManager->getCache()->get($key);
        $I->assertInstanceOf(
            Simple::class,
            $resultset,
        );
    }

    public function canOmitCacheFindAll(IntegrationTester $I)
    {
        $I->wantToTest('omitting the cache on a fetch all by query criteria');

        $data = $this->data();
        $criteria = $this->repository
            ->createCriteria()
            ->like('email', $data['like']['email'])
        ;
        $result = $this->repository->nocache()->find($criteria);
        // generate the key to fetch
        // and validate the data from cache
        $query = $criteria
            ->createBuilder()
            ->getQuery()
        ;
        $key = $this->cacheManager->generateKey(
            $this->repository->getModel(),
            $query,
        );

        // no key present since cache omitted
        $I->assertFalse(
            $this->cacheManager->getCache()->has($key)
        );

        // executing a second request should use cache
        $result = $this->repository->find($criteria);

        $I->assertTrue(
            $this->cacheManager->getCache()->has($key)
        );

        $resultset = $this->cacheManager->getCache()->get($key);

        $I->assertInstanceOf(
            Simple::class,
            $resultset,
        );
    }

    public function canCacheFetchRelatedModels(IntegrationTester $I)
    {
        $I->wantToTest('caching related models for a relationship alias definition');

        $data = $this->data();
        $model = $this->repository->findByPk($data['primaryKey']);
        $alias = $data['alias'];
        $result = $this->repository->{"get$alias"}(
            $model,
        );
        $criteria = [
            'id' => $model->getId(),
            'relation' => $alias,
        ];
        // generate the key to fetch
        // and validate the data from cache
        $key = $this->cacheManager->generateKey(
            $this->repository->getModel(),
            $criteria,
        );
        $resultset = $this->cacheManager->getCache()->get($key);

        $I->assertInstanceOf(
            ResultsetInterface::class,
            $resultset,
        );
    }

    public function canOmitCacheFetchRelatedModels(IntegrationTester $I)
    {
        $I->wantToTest('omitting the cache on fetching related models for a relationship alias definition');

        $data = $this->data();
        $model = $this->repository->findByPk($data['primaryKey']);
        $alias = $data['alias'];
        $result = $this->repository->nocache()->{"get$alias"}(
            $model,
        );
        $criteria = [
            'id' => $model->getId(),
            'relation' => $data['alias'],
        ];
        // generate the key to fetch
        // and validate the data from cache
        $key = $this->cacheManager->generateKey(
            $this->repository->getModel(),
            $criteria,
        );

        // no key present since cache omitted
        $I->assertFalse(
            $this->cacheManager->getCache()->has($key)
        );

        // executing a second request should use cache
        $result = $this->repository->{"get$alias"}(
            $model,
        );

        $I->assertTrue(
            $this->cacheManager->getCache()->has($key)
        );

        $resultset = $this->cacheManager->getCache()->get($key);

        $I->assertInstanceOf(
            ResultsetInterface::class,
            $resultset,
        );
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
            'limit' => 5,
            'offsetId' => 25,
            'primaryKey' => 1,
            'queryBuilderPhql' => 'SELECT [Stub\Domain\Model\User].* FROM [Stub\Domain\Model\User] WHERE id > :ID0: GROUP BY [name] ORDER BY id',
        ];
    }
}
