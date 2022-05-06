<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Unit\Repository;

use Headio\Phalcon\ServiceLayer\Model\Criteria;
use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Repository\QueryRepository;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Query\Builder;
use Stub\Domain\Model\Role as Model;
use Stub\Domain\Repository\Role as Repository;
use Mockery;
use Module\UnitTest;
use DateTimeImmutable;

class RepositoryTest extends UnitTest
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

    public function testReturnModelManagedByRepository(): void
    {
        $this->specify(
            'Can return the model managed by the repository',
            function () {
                $m = Mockery::mock(Repository::class);
                $m
                ->shouldReceive('getModel')
                ->once()
                ->andReturn(Model::class);

                $m->getModel();
            }
        );
    }

    public function testReturnInstanceOfQueryCriteria(): void
    {
        $this->specify(
            'Can return an instance of the criteria interface',
            function () {
                $m = Mockery::mock(QueryRepository::class);
                $m
                ->shouldReceive('createCriteria')
                ->once()
                ->andReturn(new Criteria());

                $m->createCriteria();
            }
        );
    }

    public function testReturnInstanceOfQueryBuilder(): void
    {
        $this->specify(
            'Can return an instance of the query builder interface',
            function () {
                $m = Mockery::mock(QueryRepository::class);
                $m
                ->shouldReceive('createBuilder')
                ->once()
                ->andReturn(new Builder());

                $m->createBuilder();
            }
        );
    }

    public function testFetchRowCount(): void
    {
        $this->specify(
            'Can fetch the row count for query criteria',
            function () {
                $m = Mockery::mock(QueryRepository::class);
                $m
                ->shouldReceive('fetchColumn')
                ->once()
                ->with(CriteriaInterface::class)
                ->andReturn(
                    1
                )
                ->shouldReceive('fetchColumn')
                ->with(CriteriaInterface::class);

                $criteria = (new Repository())
                    ->createCriteria()
                    ->columns(['c' => 'COUNT(*)'])
                ;

                $m->fetchColumn(
                    $criteria
                );
            }
        );
    }

    public function testFetchRecordByColumnName(): void
    {
        $this->specify(
            'Can fetch a record column value',
            function () {
                $expected = [
                    'id' => 1,
                    'label' => 'Admin',
                    'created' => new DateTimeImmutable("@1541926960"),
                    'modified' => new DateTimeImmutable("@1551814121"),
                ];

                $m = Mockery::mock(QueryRepository::class);
                $m
                ->shouldReceive('fetchColumn')
                ->once()
                ->with(CriteriaInterface::class)
                ->andReturn(
                    $expected['label'],
                );

                $criteria = (new Repository())
                    ->createCriteria()
                    ->eq('id', $expected['id'])
                    ->columns(['label'])
                ;

                $m->fetchColumn(
                    $criteria
                );
            }
        );
    }

    public function testFindByPropertyName()
    {
        $expected = [
            'id' => 1,
            'label' => 'Admin',
            'created' => new DateTimeImmutable("@1541926960"),
            'modified' => new DateTimeImmutable("@1551814121"),
        ];

        $m = Mockery::mock(QueryRepository::class);
        $m
        ->shouldReceive('findFirstBy')
        ->once()
        ->with(Mockery::type('string'), $expected['id'])
        ->andReturn(
            new Model($expected),
        );

        $m->findFirstBy('id', $expected['id']);
    }

    public function testFindByPrimaryKey()
    {
        $expected = [
            'id' => 1,
            'label' => 'Admin',
            'created' => new DateTimeImmutable("@1541926960"),
            'modified' => new DateTimeImmutable("@1551814121"),
        ];

        $m = Mockery::mock(QueryRepository::class);
        $m->shouldReceive('findByPk')
            ->once()
            ->with($expected['id'])
            ->andReturn(
                new Model($expected),
            )
        ;

        $m->findByPk($expected['id']);
    }

    public function testFindByQueryCriteria()
    {
        $expected = [
            'id' => 1,
            'label' => 'Admin',
            'created' => new DateTimeImmutable("@1541926960"),
            'modified' => new DateTimeImmutable("@1551814121"),
        ];

        $m = Mockery::mock(QueryRepository::class);
        $r = Mockery::mock(Repository::class);
        $c = Mockery::mock(Criteria::class);
        $r->shouldReceive('createCriteria')
            ->once()
            ->andReturn($c)
        ;
        $m->shouldReceive('find')->once()->with($c);
        $c->shouldReceive('eq')
            ->with('label', $expected['label'])
            ->andReturnSelf()
        ;
        $c = $r->createCriteria();
        $c->eq('label', $expected['label']);
        $m->find($c);
    }
}
