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
use Headio\Phalcon\ServiceLayer\Filter\OrderBy;
use IntegrationTester;

class OrderByCest
{
    public function canCreateOrderByConstraint(IntegrationTester $I)
    {
        $I->wantToTest('creating an order by constaint');
        $args = [
            'column' => 'name',
            'direction' => null,
        ];
        $orderBy = new OrderBy(
            ...array_values($args)
        );

        $I->assertEquals($args['column'], $orderBy->getColumn());
        $I->assertEquals(null, $orderBy->hasDirection());
    }

    public function canCreateAscOrderByConstraint(IntegrationTester $I)
    {
        $I->wantToTest('creating an order by constraint of type `asc`');
        $args = [
            'column' => 'name',
            'direction' => OrderBy::ASC,
        ];
        $orderBy = new OrderBy(
            ...array_values($args)
        );

        expect($orderBy->getColumn())->equals($args['column']);
        expect($orderBy->getDirection())->equals(null);
    }

    public function canCreateDescOrderByConstraint(IntegrationTester $I)
    {
        $I->wantToTest('creating an order by constraint of type `desc`');
        $args = [
            'column' => 'name',
            'direction' => OrderBy::DESC,
        ];
        $orderBy = new OrderBy(
            ...array_values($args)
        );

        $I->assertEquals($args['column'], $orderBy->getColumn());
        $I->assertEquals(OrderBy::DESC, $orderBy->getDirection());
    }

    public function canAppendOrderByConstraintToFilterCriteria(IntegrationTester $I)
    {
        $I->wantToTest('appending an order by constraint to the filter criteria');
        $filter = new Filter();
        $args = [
            'column' => 'name',
            'direction' => null,
        ];
        $filter->orderBy(
            $args['column'],
            $args['direction'],
        );

        $I->assertTrue($filter->hasOrderBy());
        $I->assertIsArray($filter->getOrderBy());
    }

    public function canAppendDescOrderByConstraintToFilterCriteria(IntegrationTester $I)
    {
        $I->wantToTest('appending an order by constraint of type `desc` to the filter criteria');
        $filter = new Filter();
        $args = [
            'column' => 'name',
            'direction' => OrderBy::DESC,
        ];
        $filter->orderBy(
            $args['column'],
            $args['direction'],
        );

        $I->assertTrue($filter->hasOrderBy());
        $I->assertIsArray($filter->getOrderBy());
    }
}
