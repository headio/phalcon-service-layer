# Phalcon service layer

A simple repository service implementation for Phalcon 5 projects

[![Build Status](https://travis-ci.com/headio/phalcon-service-layer.svg?branch=5.x)](https://travis-ci.com/headio/phalcon-service-layer) [![Coverage Status](https://coveralls.io/repos/github/headio/phalcon-service-layer/badge.svg?branch=5.x)](https://coveralls.io/github/headio/phalcon-service-layer?branch=5.x)

## Introduction

This library provides a layered architecture promising easier unit and integration testing.

The service layer handles business logic, mediating between the application layer (controller or handler) and the domain, interacting with a single repository or multiple repositories. All repositories extend an abstract **query repository**, providing a collection-like interface, with well-defined query methods. Hence all queries are isolated in the repository layer.

Phalcon ORM implements the active record pattern, therefore the responsiblity of persistence remains with the active record, in contrast to the repository service pattern / data mapper (Doctrine), where repositories manage the entity lifecycle.

If you have been reading between the lines, you have probably gathered this is a hybrid solution offering: testability, reuseability and prevention of logic leaking into the application layer. The trade-off is you need to write, test and maintain some extra boiler-plate code.

Naturally, you can avoid this paradigm by integrating a data mapper (Doctrine, Atlas ORM etc.) with Phalcon. Nevertheless, for those enjoying the performance of Phalcon ORM, this library may be of interest.

## Dependencies

* PHP >=8.0.0 <=8.0.99
* Phalcon 5.0.0+

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
│   │    │── Model
│   │    │    │── Foo
│   │    │── Repository
│   │    │    │── Foo
│   │    │── Service
│   │    │    │── Foo
│   │── Provider
│   │    │── FooService
└──
```

### Registering a service provider

Create a new **Foo** service dependency inside the service provider directory **/src/Provider/**.

```php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Repository\Foo as Repository;
use App\Domain\Service\Foo as Service;
use Headio\Phalcon\ServiceLayer\Repository\Factory;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class Foo implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'fooService',
            function () {
                $repository = Factory::create(Repository::class)
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
use Phalcon\Di\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    /**
     * {@inheritDoc}
     */
    public function registerAutoloaders(DiInterface $container = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function registerServices(DiInterface $container)
    {
        $container->setShared(
            'fooService',
            function () use ($container) {
                $repository = Factory::create(Repository::class);
                $service = new Service($repository);

                return $service;
            }
        );
    }
}
```

### Controller/Handler

Now the service is in place, the controller can interact with the service layer by injecting the service into the controller via the **OnConstruct** method.

```php
namespace App\Module\Admin\Foo;

use Phalcon\Mvc\Controller;

class Foo extends Controller
{
    private FooInterface $service;

    /**
     * Inject service layer dependencies
     */
    public function onConstruct(): void
    {
        $this->service = $this->getDI()->get('fooService');
    }
}
```

### Service layer

The service layer interacts with one repository (or multiple repositories) to process the business logic. In the example below, the service calls the delete method (implementation skipped for simplicity) to remove a model instance by primary key and return to the list view.

```php
declare(strict_types=1);

namespace App\Domain\Service\Foo;

use App\Domain\Repository\FooInterface;
use App\Domain\Service\FooInterface as ServiceInterface;
use Phalcon\Di\Injectable;
use Phalcon\Http\ResponseInterface;

class Foo extends Injectable implements ServiceInterface
{
    public function __construct(private FooInterface $repository)
    {
    }

    /**
     * Delete a model instance
     */
    public function deleteModel(int $id): ResponseInterface
    {
        $model = $this->repository->findByPk($id);

        if ($this->delete($model)) {
            $this->flashSession->notice('Task completed');
            return $this->response->redirect(['for' => 'adminFoos']);
        }
    }
}
```

### Repository

All repositories must extend the abstract query repository and implement one abstract method.

```php
declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Foo as Model;
use App\Domain\Repository\FooInterface;
use Headio\Phalcon\ServiceLayer\Repository\QueryRepository;

class Foo extends QueryRepository implements FooInterface
{
    /**
     * Return the model name managed by this repository.
     */
    protected function getModelName(): string
    {
        return Model::class;
    }
}
```

The **Foo** repository can implement additional interfaces, e.g. **FooInterface**, providing further concrete methods for the service layer.

The abstract query repository implements the following repository interface:

```php
declare(strict_types=1);

namespace Headio\Phalcon\Repository\Repository;

use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;

interface RepositoryInterface
{
    /**
     * Return an instance of the query criteria pre-populated
     * with the model managed by this repository.
     */
    public function createCriteria(): CriteriaInterface;

    /**
     * Return an instance of the query builder.
     */
    public function createBuilder(array $params = null): BuilderInterface;

    /**
     * Fetch a column value by query criteria from storage.
     */
    public function fetchColumn(CriteriaInterface $criteria): mixed;

    /**
     * Fetch records by query criteria from storage.
     */
    public function find(CriteriaInterface $criteria): ResultsetInterface;

    /**
     * Fetch record by primary key from storage.
     */
    public function findByPk(int $id): ModelInterface;

    /**
     * Fetch first record by query criteria from storage.
     */
    public function findFirst(CriteriaInterface $criteria): ModelInterface;

    /**
     * Fetch first record by property name from storage.
     */
    public function findFirstBy(string $property, mixed $value): ModelInterface;

    /**
     * Return the fully qualified (or unqualified) class name
     * for the model managed by this repository.
     */
    public function getModel(bool $unqualified = false): string;

    /**
     * Return the related models from storage.
     */
    public function getRelated(
        string $alias,
        ModelInterface $model,
        CriteriaInterface $criteria = null,
    ): ResultsetInterface|bool|int;
}
```

In addition, a relationship trait is provided to simplify handling model relationships.

```php
declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Foo as Model;
use App\Domain\Repository\FooInterface;
use Headio\Phalcon\ServiceLayer\Repository\QueryRepository;
use Headio\Phalcon\ServiceLayer\Repository\Traits\RelationshipTrait;

class Foo extends QueryRepository implements FooInterface
{
    use RelationshipTrait;

    /**
     * Return the model name managed by this repository.
     */
    protected function getModelName(): string
    {
        return Model::class;
    }
}
```

#### Query caching

Query caching is handled utilizing Phalcon's event manager.
To get started first include the **CacheableTrait** in your repository; the **EventsAwareInterface** is implemented inside the cacheable trait.

```php
declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\User as Model;
use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Headio\Phalcon\ServiceLayer\Repository\QueryRepository;
use Headio\Phalcon\ServiceLayer\Repository\Traits\CacheableTrait;
use Phalcon\Events\EventsAwareInterface;

class User extends QueryRepository implements UserInterface, EventsAwareInterface
{
    use CacheableTrait;

    /**
     * Return the model name managed by this repository.
     */
    protected function getModelName(): string
    {
        return Model::class;
    }
}
```

Then create a service provider for your service layer, or a repository if you want to omit the service layer and work with repositories directly. The example below utilizes Phalcon's service provider interface.

```php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Repository\Foo as Repository;
use App\Domain\Service\Foo as Service;
use Headio\Phalcon\ServiceLayer\Cache\Listener\CacheListener;
use Headio\Phalcon\ServiceLayer\Repository\Factory;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class Foo implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'fooService',
            function () {
                $eventsManager = new EventsManager();
                // factory instantiation
                $repository = Factory::create(Repository::class);
                $repository->setEventsManager($eventsManager);
                $cacheManager = $this->get('cacheManager');
                // attach the cache event listener and inject the
                // cache manager dependency
                $eventsManager->attach(
                    'cache',
                    new CacheListener(
                        $cacheManager
                    )
                );
                $service = new Service($repository);

                return $service;
            }
        );
    }
}
```

##### Cache event listener

The event listener provides two methods to handle caching, see below.

```php
/**
 * This event listener provides caching functionality for repositories.
 */
class CacheListener
{
    public function __construct(private ManagerInterface $manager)
    {
    }

    /**
     * Appends a cache declaration to a Phalcon query instance.
     */
    public function append(
        EventInterface $event,
        RepositoryInterface $repository,
        QueryInterface $query,
    ): QueryInterface;

    /**
     * Fetches data from cache or storage using the cache-aside
     * strategy.
     */
    public function fetch(
        EventInterface $event,
        RepositoryInterface $repository,
        array $context,
    ): ModelInterface|ResultsetInterface;
}
```

To trigger a cache event, see the following concrete examples from the cacheable trait.

```php
trait CacheableTrait
{
    /**
     * Fetch first record by query criteria from cache or storage.
     *
     * @throws NotFoundException
     */
    public function findFirst(CriteriaInterface $criteria): ModelInterface
    {
        $query = $criteria
            ->createBuilder()
            ->getQuery()
            ->setUniqueRow(true)
        ;
        $this->eventsManager->fire('cache:append', $this, $query);
        $model = $query->execute();

        if (!$model instanceof ModelInterface) {
            throw new NotFoundException('404 Not Found');
        }

        return $model;
    }

    /**
     * Fetch data from cache or storage.
     */
    public function fromCache(
        QueryInterface|array $query,
        Closure $callable,
        DateInterval|int $lifetime = null,
    ): ResultsetInterface|ModelInterface|null {
        $key = $this->cacheManager->generateKey(
            $this->getModel(),
            $query,
        );

        return $this->eventsManager->fire(
            'cache:fetch',
            $this,
            [$key, $callable],
        );
    }
```

### Pagination

This library provides a cursor-based paginator adapter; see **_stub** directory inside the test directory for usage.

### The model

All models must extend the abstract **Model** class, which implements the following model interface:

```php
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Model;

use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Model\CriteriaInterface;

interface ModelInterface
{
    /**
     * Return the model primary key attribute.
     */
    public function getPrimaryKey(): string;

    /**
     * Return the property binding type for a given property.
     */
    public function getPropertyBindType(string $property): int;

    /**
     * Return the model validation errors as an array representation.
     */
    public function getValidationErrors(): array;

    /**
     * Return an instance of the query criteria pre-populated
     * with the model.
     */
    public static function query(DiInterface $container = null): CriteriaInterface;
}
```

### Validation

Validation can be implemented in the service layer or the model classes.

## Testing

To see the tests, run:

```bash
php vendor/bin/codecept run -f --coverage --coverage-xml
```

## License

Phalcon service layer is open-source and licensed under [MIT License](http://opensource.org/licenses/MIT).
