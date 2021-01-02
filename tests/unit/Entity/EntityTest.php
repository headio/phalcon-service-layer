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

namespace Unit\Entity;

use Headio\Phalcon\ServiceLayer\Entity\AbstractEntity;
use Headio\Phalcon\ServiceLayer\Entity\EntityInterface;
use Phalcon\Db\Column;
use Stub\Domain\Entity\Role as Entity;
use Module\UnitTest;

class EntityTest extends UnitTest
{
    private $entity;

    protected function _before() : void
    {
        parent::_before();

        $this->entity = new Entity();
    }

    protected function after() : void
    {
        parent::after();
    }

    public function testInheritance() : void
    {
        $this->specify(
            'Entity should inherit expected abstract entity',
            function () {
                expect($this->entity)->isInstanceOf(AbstractEntity::class);
            }
        );

        $this->specify(
            'Entity should implement expected interface',
            function () {
                expect($this->entity)->isInstanceOf(EntityInterface::class);
            }
        );
    }

    public function testGetPrimarykey() : void
    {
        $this->specify(
            'Entity should return expected primary key attribute',
            function () {
                expect($this->entity->getPrimarykey())->equals($this->_data()['pk']);
            }
        );
    }

    public function testGetPropertyBindType() : void
    {
        $this->specify(
            'Entity should return expected attribute bind type',
            function () {
                expect($this->entity->getPropertyBindType('label'))->equals($this->_data()['label']);
            }
        );
    }

    /**
     * Return test data
     */
    protected function _data() : array
    {
        return [
            'pk' => 'id',
            'label' => Column::BIND_PARAM_STR
        ];
    }
}
