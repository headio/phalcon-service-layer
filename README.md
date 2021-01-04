# Phalcon service layer

A simple service layer implementation for Phalcon projects

[![Build Status](https://travis-ci.com/headio/phalcon-service-layer.svg?branch=master)](https://travis-ci.com/headio/phalcon-service-layer) [![Coverage Status](https://coveralls.io/repos/github/headio/phalcon-service-layer/badge.svg?branch=master)](https://coveralls.io/github/headio/phalcon-service-layer?branch=master)

## Introduction

This library provides a layered architecture promising easier unit and integration testing.

The service layer handles business logic, mediating between the application layer (controller or handler) and the domain, interacting with a single repository or multiple repositories. All repositories extend an abstract **query repository**, providing a collection-like interface, with well-defined query methods, encapsulating filtering and caching. Hence all queries are isolated in the repository layer.

Phalcon ORM implements the active record pattern, therefore the responsiblity of persistence remains with the active record, in contrast to the repository pattern / data mapper (Doctrine), where repositories manage the entity lifecycle.

If you have been reading between the lines, you have probably gathered this is a hybrid solution offering: testability, reuseability and prevention of logic leaking into the application layer. The trade-off is you need to write, test and maintain some extra boiler-plate code.

Naturally, you can avoid this paradigm by integrating a data mapper (Doctrine, Atlas ORM etc.) with Phalcon. Nevertheless, for those enjoying the performance of Phalcon ORM, this library may be of interest.

## Dependencies

* PHP 7.4+
* Phalcon 4.1.0+

## Installation

### Composer

Open a terminal window and run:

```bash
composer require headio/phalcon-service-layer
```

## Usage

Assuming the following project structure, let's create the layers to handle removing a record from storage as a simple usage example.

```bash
├── src
│   │── Module
│   │    │── Admin
│   │    │    │── Controller
│   │    │    │    │── Foo
│   │    │    │── Module.php
│   │── Domain
│   │    │── Entity
│   │    │    │── Foo
│   │    │── Filter
│   │    │    │── Foo
│   │    │── Repository
│   │    │    │── Foo
│   │    │── Service
│   │    │    │── Foo
│   │── Service
└──
```

### Registering the service

Create a new **Foo** service dependency inside the service provider directory **/src/Service/**.

```php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Repository\Foo as Repository;
use App\Domain\Service\Foo as Service;
use Headio\Phalcon\ServiceLayer\Repository\Factory;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class Foo implements ServiceProviderInterface
{
    public function register(DiInterface $di) : void
    {
        $di->setShared(
            'fooService',
            function () use ($di) {
                /** @var bool */
                $cache = $di->getConfig()->cache->modelCache->apply;
                $repository = Factory::create(Repository:class, $cache)
                $service = new Service($repository);

                return $service;
            }
        );
    }
}
```

Alternatively, create the dependency on a per-module basis.

```php
declare(strict_types=1);

namespace App\Module\Admin;

use App\Domain\Repository\Foo as Repository;
use App\Domain\Service\Foo as Service;
use Headio\Phalcon\ServiceLayer\Repository\Factory;
use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    /**
     * {@inheritDoc}
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function registerServices(DiInterface $di)
    {
        $di->setShared(
            'fooService',
            function () use ($di) {
                /** @var bool */
                $cache = $di->getConfig()->cache->modelCache->apply;
                $repository = Factory::create(Repository:class, $cache)
                $service = new Service($repository);

                return $service;
            }
        );
    }
}
```

### The controller

Now the service is in place, the controller can interact with the service layer by injecting the service into the controller via the **OnConstruct** method.
To remove the record, the controller calls the  **deleteModel** method on the service.

```php
namespace App\Module\Admin\Foo;

use Phalcon\Mvc\Controller;

class Foo extends Controller
{
    /**
     * @var App\Domain\Service\FooInterface
     */
    private $service;

    /**
     * Inject service layer dependencies
     */
    public function onConstruct() : void
    {
        $this->service = $this->getDI()->get('fooService');
    }

    /**
     * Delete a model instance
     *
     * @Route("/delete/{id:[0-9]+}", methods={"GET"}, name="adminFooDelete")
     */
    public function deleteAction(int $id)
    {
        try {
            $id = $this->filter->sanitize($id, Filter::FILTER_ABSINT);
            return $this->service->deleteModel($id);
        } catch (Throwable $e) {
            $message = $this->handleException($e);
            $this->flashSession->error($message);
            return $this->response->redirect(['for' => 'adminFoos']);
        }
    }
}
```

### The service layer

To remove the record, the service interacts with the repository. If the record exists, the service calls the delete method (implementation skipped for simplicity); on success the user is redirected to the table view.

```php
declare(strict_types=1);

namespace App\Domain\Service\Foo;

use App\Domain\Filter\Foo as QueryFilter;
use App\Domain\Repository\FooInterface;
use Phalcon\Di\Injectable;
use Phalcon\Http\ResponseInterface;

class Foo extends Injectable
{
    /**
     * @var FooInterface
     */
    private $repository;

    public function __construct(FooInterface $fooRepository)
    {
        $this->repository = $fooRepository;
    }

    /**
     * Delete a model instance
     */
    public function deleteModel(int $id) : ResponseInterface
    {
        $entity = $this->repository->findByPk($id);

        if ($this->delete($entity)) {
            $this->flashSession->notice('Task completed');
            return $this->response->redirect(['for' => 'adminFoos']);
        }
    }
}
```

### The repository

The **Foo** repository extends the abstract query repository, implementing the following abstract methods. The repository could implement additional interfaces, e.g. **FooInterface**, providing further concrete methods for the service layer.

```php
declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Filter\Foo as QueryFilter;
use App\Domain\Repository\FooInterface;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Headio\Phalcon\ServiceLayer\Repository\QueryRepository;

class Foo extends QueryRepository implements FooInterface
{
    /**
     * Return an instance of the query filter used with this repository.
     */
    public function getQueryFilter() : FilterInterface
    {
        return new QueryFilter();
    }

    /**
     * Return the entity name managed by this repository.
     */
    protected function getEntityName() : string
    {
        return 'App\\Domain\\Entity\\Foo';
    }
}
```

### The query repository

The query repository implements the following interface:

```php
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Repository;

use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Headio\Phalcon\ServiceLayer\Filter\FilterInterface;
use Phalcon\Mvc\Model\CriteriaInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;

interface RepositoryInterface
{
    /**
     * Apply the cache to the query criteria.
     */
    public function applyCache(QueryInterface $query, CriteriaInterface $criteria) : void;

    /**
     * Apply the filter to the query criteria.
     */
    public function applyFilter(CriteriaInterface $criteria, FilterInterface $filter) : void;

    /**
     * Fetch row count from cache or storage.
     */
    public function count(FilterInterface $filter) : int;

    /**
     * Return an instance of the query criteria pre-populated
     * with the entity managed by this repository.
     */
    public function createCriteria() : CriteriaInterface;

    /**
     * Return an instance of the query builder pre-populated
     * for the entity managed by this repository.
     */
    public function createQuery(array $params = null, ?string $alias = null) : BuilderInterface;

    /**
     * Fetch column value by query criteria.
     *
     * @return mixed
     */
    public function fetchColumn(CriteriaInterface $criteria);

    /**
     * Fetch records by filter criteria from cache or storage.
     */
    public function find(FilterInterface $filter) : ResultsetInterface;

    /**
     * Fetch record by primary key from cache or storage.
     */
    public function findByPk(int $id) : EntityInterface;

    /**
     * Fetch first record by filter criteria from cache or storage.
     */
    public function findFirst(FilterInterface $filter) : EntityInterface;

    /**
     * Fetch first record by property name from cache or storage.
     */
    public function findFirstBy(string $property, $value) : EntityInterface;

    /**
     * Return the fully qualified (or unqualified) class name
     * for the entity managed by the repository.
     */
    public function getEntity(bool $unqualified = false) : string;

    /**
     * Return the related models from cache or storage.
     */
    public function getRelated(string $alias, EntityInterface $entity, FilterInterface $filter) : ResultsetInterface;

    /**
     * Return the unrelated models from cache or storage.
     */
    public function getUnrelated(ResultsetInterface $resultset, FilterInterface $filter) : ResultsetInterface;
}
```

### The filter interface

Repositories can utilize the query filter to build filter criteria. The filter criteria are applied to the query criteria before executing queries. See the following usage examples inside an arbitrary repository:

```php
/**
 * Return a collection of models filtered by primary keys
 * from cache or storage.
 */
public function getModelsByPrimaryKeys(array $keys) : ResultsetInterface
{
    $entityName = $this->getEntity();
    $filter = $this->getQueryFilter()
        ->in((new $entityName)->getPrimaryKey(), $keys);

    return $this->find($filter);
}

/**
 * Return the primary key value by query criteria
 * from cache or storage.
 *
 * @return mixed
 */
public function getPrimaryKeyForResource(string $label)
{
    $criteria = $this->createCriteria()->columns(['id']);
    $filter = $this->getQueryFilter()->eq('label', $label);
    $this->applyFilter($criteria, $filter);
    $result = $this->fetchColumn($criteria);

    return $result;
}

/**
 * Return the filter for the table view.
 */
public function createFilter(int $offset, int $limit, string $key) : FilterInterface
{
    $store = new SessionBag($key);
    $filter = $this->getQueryFilter()
        ->alias($this->getEntityName())
        ->orderBy('id')
        ->limit($limit * 3);

    if ($offset > 0) {
        $filter->offset($offset);
    }

    if (!empty($keyword = $this->request->getQuery('keyword', Filter::FILTER_STRING))) {
        $store->keyword = $keyword;
    }

    if ($this->request->get('clear', Filter::FILTER_INT)) {
        $store->destroy();
    }

    if ($store->has('keyword')) {
        $filter->setKeyword($store->keyword);
    }

    return $filter;
}
```

### The entity

The simple repository pattern relies on Getter/Setter implementation for public properties. All models must extend the **AbstractEntity** class, which implements the following entity interface:

```php
declare(strict_types=1);

/**
 * Entity Interface
 */
interface EntityInterface
{
    /**
     * Return the entity primary key attribute.
     */
    public function getPrimaryKey() : string;

    /**
     * Return the property binding type for a given property.
     */
    public function getPropertyBindType(string $property) : int;

    /**
     * Return the model validation errors as an array representation.
     */
    public function getValidationErrors() : array;
}
```

A repository trait is implemented to simplify handling many-to-many model relationships. This implementation requires both the @hasMany and @hasManyToMany relationship definitions on the source entity. See the following example:

```php
declare(strict_types=1);

namespace App\Domain\Entity;

use Headio\Phalcon\ServiceLayer\Entity\AbstractEntity;
use Headio\Phalcon\ServiceLayer\Entity\TimestampTrait;
use Headio\Phalcon\ServiceLayer\Entity\Behavior\CacheInvalidateable;
use Headio\Phalcon\ServiceLayer\Entity\Behavior\Timestampable;

/**
 * @Source("Resource")
 *
 * @HasManyToMany(
 *     "id",
 *     "App\Domain\Entity\ResourceUser",
 *     "resource_id",
 *     "user_id",
 *     "App\Domain\Entity\User",
 *     "id", {
 *         "alias" : "users",
 *         "params": {
 *             "order" : "[App\Domain\Entity\User].[id] DESC"
 *         }
 *     }
 * )
 *
 * @HasMany(
 *     "id",
 *     "App\Domain\Entity\ResourceUser",
 *     "resource_id",
 *     {
 *         "alias" : "resourceUsers"
 *     }
 * )
 */
class Resource extends AbstractEntity
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", length="10")
     */
    protected $id;

    /**
     * @Column(type="string", nullable=false, column="label", length="64")
     */
    protected $label;

    /**
     * Use trait for timestamp functionality.
     */
    use TimestampTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize() : void
    {
        parent::initialize();

        $this->addBehavior(new Timestampable());
        $this->addBehavior(new CacheInvalidateable(
            [
                'invalidate' => [
                    'App\\Domain\\Entity\\Role',
                    'App\\Domain\\Entity\\User'
                ]
            ]
        ));
    }

    public function getId() : int
    {
        return (int) $this->id;
    }

    public function getLabel() : ?string
    {
        return $this->label;
    }

    public function setLabel(string $input) : void
    {
        $this->label = $input;
    }
}
```

### Validation

Validation can be implemented in the service layer or in models.

## Testing

To see the tests, run:

```bash
php vendor/bin/codecept run -f --coverage --coverage-xml
```

## License

Phalcon service layer is open-source and licensed under [MIT License](http://opensource.org/licenses/MIT).
