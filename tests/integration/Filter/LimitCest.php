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

class LimitCest
{
    public function canCreateLimitConstraint(IntegrationTester $I)
    {
        $I->wantToTest('creating a limit constaint');
        $args = [
            'limit' => 5,
        ];
        $filter = new Filter();
        $filter->limit(
            ...array_values($args)
        );

        $I->assertTrue($filter->hasLimit());
        $I->assertEquals($args['limit'], $filter->getLimit());
    }
}
