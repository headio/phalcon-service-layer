# Phalcon domain layer

This is a domain layer implementation for Phalcon applications.

## Description

The domain layer implements business logic and interacts with the application layer (controller) leading to thin controllers decoupled from busines logic. This library provides a layered architecture offering a separation of concerns and easier unit and integration testing.
The service layer handles business logic, interacting with a single repository or multiple repositories. The repository layer handles queries from the service layer, hence all queries are isolated in the repository layer. All repositories extend an abstract **query repository**, providing an array of well-defined query methods; this is not the same as the repository pattern / data mapper (Doctrine, Spring), where repositories manage the entity lifecycle. Phalcon ORM implements the active record pattern, and therefore, the query repository is simply a collection-like interface.

## Dependencies

* PHP7.2+
* Phalcon3.4+ < 4.0

## Installation

### Composer

Open a terminal window and run:

```bash
composer require headio/phalcon-bootstrap
```

## Usage

Coming soon!

## Testing

To see the tests, run:

```bash
php vendor/bin/codecept run -f --coverage --coverage-text
```

## License

Phalcon domain layer is open-source and licensed under [MIT License](http://opensource.org/licenses/MIT).
