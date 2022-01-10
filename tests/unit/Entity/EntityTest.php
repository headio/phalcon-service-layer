<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Unit\Entity;

use Headio\Phalcon\ServiceLayer\Entity\AbstractEntity;
use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Phalcon\Db\Column;
use Stub\Domain\Entity\Role;
use Module\UnitTest;

class EntityTest extends UnitTest
{
    private Role $model;

    protected function _before(): void
    {
        parent::_before();

        $this->model = new Role();
    }

    public function testInheritance(): void
    {
        $this->specify(
            'Model should inherit expected abstract entity',
            function () {
                expect(
                    $this->model
                )->isInstanceOf(AbstractEntity::class);
            }
        );

        $this->specify(
            'Model should implement expected interface',
            function () {
                expect(
                    $this->model
                )->isInstanceOf(EntityInterface::class);
            }
        );
    }

    public function testGetPrimarykey(): void
    {
        $this->specify(
            'Model should return expected primary key attribute',
            function () {
                expect(
                    $this->model->getPrimarykey()
                )->equals($this->_data()['pk']);
            }
        );
    }

    public function testGetPropertyBindType(): void
    {
        $this->specify(
            'Model should return expected attribute bind type',
            function () {
                expect(
                    $this->model->getPropertyBindType('label')
                )
                ->equals($this->_data()['label']);
            }
        );
    }

    private function _data(): array
    {
        return [
            'pk' => 'id',
            'label' => Column::BIND_PARAM_STR
        ];
    }
}
