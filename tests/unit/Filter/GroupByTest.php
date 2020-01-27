<?php
/*
 * This source file is subject to the MIT License.
 *
 * (c) Dominic Beck <dominic@headcrumbs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this package.
 */
declare(strict_types=1);

namespace Unit\Filter;

use Headio\Phalcon\ServiceLayer\Filter\{ GroupBy, GroupByInterface };
use Mockery;
use Module\UnitTest;

class GroupByTest extends UnitTest
{
    protected function _before() : void
    {
        parent::_before();
    }

    protected function _after() : void
    {
        parent::_after();
    }

    public function testCanCreateGroupByConstraint() : void
    {
        $this->specify(
            'Can create a group by query constraint',
            function () {
                $mock = Mockery::mock(
                    GroupBy::class,
                    GroupByInterface::class,
                    [
                        $this->_data()['attribute']
                    ]
                );

                $mock->allows()->getColumn()->andReturn($this->_data()['attribute']);

                expect($mock->getColumn())->equals($this->_data()['attribute']);
            }
        );
    }

    /**
     * Return test data
     */
    public function _data() : array
    {
        return [
            'attribute' => 'name',
        ];
    }
}
