<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Unit\Model;

use Headio\Phalcon\ServiceLayer\Model\CriteriaInterface;
use Headio\Phalcon\ServiceLayer\Model\AbstractModel as Model;
use Headio\Phalcon\ServiceLayer\Model\ModelInterface;
use Phalcon\Db\Column;
use Stub\Domain\Model\Role;
use Module\UnitTest;

class ModelTest extends UnitTest
{
    protected function _before(): void
    {
        parent::_before();
    }

    protected function _after(): void
    {
        parent::_after();
    }

    public function testReturnPrimaryKeyDefinition(): void
    {
        $this->specify(
            'Model should return expected primary key attribute',
            function () {
                expect(
                    (new Role())->getPrimarykey()
                )->equals('id');
            }
        );
    }

    public function testReturnPropertyBindTypeDefinition(): void
    {
        $this->specify(
            'Model should return expected attribute bind type',
            function () {
                expect(
                    (new Role())->getPropertyBindType('label')
                )->equals(Column::BIND_PARAM_STR);
            }
        );
    }

    public function testReturnQueryCriteria(): void
    {
        $this->specify(
            'Model should return instance of query criteria',
            function () {
                expect(
                    Role::query($this->di),
                )->isInstanceOf(CriteriaInterface::class);
            }
        );
    }

}
