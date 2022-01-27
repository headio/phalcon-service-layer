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
use IntegrationTester;

class ColumnCest
{
    public function canCreateColumnConstraint(IntegrationTester $I)
    {
        $I->wantToTest('creating a column constaint');
        $args = [
            'column' => [
                'name',
                'email',
            ]
        ];
        $filter = new Filter();
        $filter->columns(
            ...array_values($args)
        );

        $I->assertTrue($filter->hasColumns());
        $I->assertIsArray($filter->getColumns());
        $I->assertEquals($args['column'], $filter->getColumns());
    }
}
