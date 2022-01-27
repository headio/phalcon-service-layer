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
use Headio\Phalcon\ServiceLayer\Filter\GroupBy;
use IntegrationTester;

class GroupByCest
{
    public function canCreateGroupByConstraint(IntegrationTester $I)
    {
        $I->wantToTest('creating a group by constaint');
        $args = [
            'column' => 'name',
        ];
        $groupBy = new GroupBy(
            ...array_values($args)
        );

        $I->assertEquals($args['column'], $groupBy->getColumn());
    }

    public function canAppendGroupByConstraintToFilterCriteria(IntegrationTester $I)
    {
        $I->wantToTest('appending a group by constraint to the filter criteria');
        $filter = new Filter();
        $args = [
            'column' => 'name',
        ];
        $filter->GroupBy(
            [
                $args['column']
            ],
        );

        $I->assertTrue($filter->hasGroupBy());
        $I->assertIsArray($filter->getGroupBy());
    }
}
