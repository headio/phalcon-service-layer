<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Pagination\Adapter;

use Headio\Phalcon\ServiceLayer\Paginator\Adapter\CursorInterface;
use Headio\Phalcon\ServiceLayer\Paginator\Adapter\Cursor as Paginator;
use Headio\Phalcon\ServiceLayer\Paginator\Cursor\Query as Cursor;
use Stub\Domain\Repository\Tag as Repository;
use IntegrationTester;

class CursorAdapterCest
{
    public function canCreatePaginator(IntegrationTester $I)
    {
        $I->wantToTest('creating a cursor paginator');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/tags/next/34';

        $response = $I->getApplication()->handle($_SERVER['REQUEST_URI']);

        $repository = new Repository(false);

        $itemsPerPage = 2;

        $query = new Cursor(
            34,
            false,
            true
        );

        $filter = $repository->createFilter(
            $query,
            $itemsPerPage
        );

        $models = $repository->find($filter);

        $paginator = new Paginator($models, $itemsPerPage, $query);

        $I->assertInstanceOf(CursorInterface::class, $paginator);
    }

    public function canFetchPaginatorItems(IntegrationTester $I)
    {
        $I->wantToTest('fetching the paginateable items');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/tags/next/34';

        $response = $I->getApplication()->handle($_SERVER['REQUEST_URI']);

        $repository = new Repository(false);

        $itemsPerPage = 2;

        $query = new Cursor(
            34,
            false,
            true
        );

        $filter = $repository->createFilter(
            $query,
            $itemsPerPage
        );

        $models = $repository->find($filter);

        $paginator = new Paginator($models, $itemsPerPage, $query);

        $I->assertEquals(true, is_array($paginator->getItems()));
    }

    public function canSerializePaginator(IntegrationTester $I)
    {
        $I->wantToTest('serializing the paginator object for json');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/tags/next/34';

        $response = $I->getApplication()->handle($_SERVER['REQUEST_URI']);

        $repository = new Repository(false);

        $itemsPerPage = 2;

        $query = new Cursor(
            34,
            false,
            true
        );

        $filter = $repository->createFilter(
            $query,
            $itemsPerPage
        );

        $models = $repository->find($filter);

        $paginator = new Paginator($models, $itemsPerPage, $query);

        $result = $paginator->jsonSerialize();

        $I->assertArrayHasKey('items', $result);

        $I->assertArrayHasKey('next_url', $result);

        $I->assertArrayHasKey('pageable', $result);

        $I->assertArrayHasKey('prev_url', $result);

        $I->assertArrayHasKey('items_per_page', $result);
    }

    public function canFetchPaginatorAsJsonRepresentation(IntegrationTester $I)
    {
        $I->wantToTest('fetching the paginator object as a json representation');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/tags/next/34';

        $response = $I->getApplication()->handle($_SERVER['REQUEST_URI']);

        $repository = new Repository(false);

        $itemsPerPage = 2;

        $query = new Cursor(
            34,
            false,
            true
        );

        $filter = $repository->createFilter(
            $query,
            $itemsPerPage
        );

        $models = $repository->find($filter);

        $paginator = new Paginator($models, $itemsPerPage, $query);

        $result = $paginator->toJson();

        $I->assertEquals(
            '{"items":[{"id":"32","label":"Python"},{"id":"31","label":"Ruby"}],"items_per_page":2,"next_url":"/tags/next/31","pageable":true,"prev_url":"/tags/prev/34"}',
            $result
        );
    }

    public function canRenderPaginatorLinks(IntegrationTester $I)
    {
        $I->wantToTest('rendering the cursor-based paginator controls');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/tags/next/34';

        $response = $I->getApplication()->handle($_SERVER['REQUEST_URI']);

        $repository = new Repository(false);

        $itemsPerPage = 4;

        $query = new Cursor(
            34,
            false,
            true
        );

        $filter = $repository->createFilter(
            $query,
            $itemsPerPage
        );

        $models = $repository->find($filter);

        $paginator = new Paginator($models, $itemsPerPage, $query);

        $I->assertEquals(true, is_string($paginator->renderLinks()));
    }

    public function canDeterminePaginatorCursorState(IntegrationTester $I)
    {
        $I->wantToTest('paginating after a given cursor');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/tags/next/31';

        $response = $I->getApplication()->handle($_SERVER['REQUEST_URI']);

        $repository = new Repository(false);

        $itemsPerPage = 2;

        $query = new Cursor(31, false, true);

        $filter = $repository->createFilter(
            $query,
            $itemsPerPage
        );

        $models = $repository->find($filter);

        $paginator = new Paginator($models, $itemsPerPage, $query);

        $I->assertEquals(28, ($paginator->getNextCursor()));

        $I->assertEquals('/tags/next/28', ($paginator->getNextUrl()));

        $I->assertEquals(false, ($paginator->isFirst()));

        $I->assertEquals(31, ($paginator->getPrevCursor()));

        $I->assertEquals('/tags/prev/31', ($paginator->getPrevUrl()));


        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/tags/next/28';

        $response = $I->getApplication()->handle($_SERVER['REQUEST_URI']);

        $repository = new Repository(false);

        $itemsPerPage = 2;

        $query = new Cursor(28, false, true);

        $filter = $repository->createFilter(
            $query,
            $itemsPerPage
        );

        $models = $repository->find($filter);

        $paginator = new Paginator($models, $itemsPerPage, $query);

        $I->assertEquals(26, ($paginator->getNextCursor()));

        $I->assertEquals('/tags/next/26', ($paginator->getNextUrl()));

        $I->assertEquals(false, ($paginator->isFirst()));

        $I->assertEquals(28, ($paginator->getPrevCursor()));

        $I->assertEquals('/tags/prev/28', ($paginator->getPrevUrl()));

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/tags/next/26';

        $response = $I->getApplication()->handle($_SERVER['REQUEST_URI']);

        $repository = new Repository(false);

        $itemsPerPage = 2;

        $query = new Cursor(26, false, true);

        $filter = $repository->createFilter(
            $query,
            $itemsPerPage
        );

        $models = $repository->find($filter);

        $paginator = new Paginator($models, $itemsPerPage, $query);

        $I->assertEquals(24, ($paginator->getNextCursor()));

        $I->assertEquals('/tags/next/24', ($paginator->getNextUrl()));

        $I->assertEquals(false, ($paginator->isFirst()));

        $I->assertEquals(26, ($paginator->getPrevCursor()));

        $I->assertEquals('/tags/prev/26', ($paginator->getPrevUrl()));
    }
}
