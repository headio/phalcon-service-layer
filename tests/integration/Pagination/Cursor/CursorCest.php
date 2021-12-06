<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Integration\Pagination\Cursor;

use Headio\Phalcon\ServiceLayer\Paginator\Cursor\Query as Cursor;
use IntegrationTester;

class CursorCest
{
    public function canCreateQueryCursor(IntegrationTester $I)
    {
        $I->wantToTest('creating a query cursor');

        $cursor = new Cursor(0, false, false);

        $I->assertEquals(false, $cursor->isPaging());
        $I->assertEquals(false, $cursor->isBefore());
        $I->assertEquals(false, $cursor->isAfter());
        $I->assertEquals(0, $cursor->getCursor());

        $cursor = new Cursor(10, true, false);

        $I->assertEquals(true, $cursor->isPaging());
        $I->assertEquals(true, $cursor->isBefore());
        $I->assertEquals(false, $cursor->isAfter());
        $I->assertEquals(10, $cursor->getCursor());

        $cursor = new Cursor(20, false, true);

        $I->assertEquals(true, $cursor->isPaging());
        $I->assertEquals(false, $cursor->isBefore());
        $I->assertEquals(true, $cursor->isAfter());
        $I->assertEquals(20, $cursor->getCursor());
    }
}
