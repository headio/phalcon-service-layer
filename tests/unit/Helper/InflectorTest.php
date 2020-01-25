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

namespace Unit\Helper;

use Headio\Phalcon\DomainLayer\Helper\Inflector;
use Module\UnitTest;
use Phalcon\Di;

class InflectorTest extends UnitTest
{
    /**
     * @var Inflector
     */
    private $service;

    protected function _before() : void
    {
        parent::_before();

        $this->service = new Inflector();
    }

    protected function _after() : void
    {
        parent::_after();
    }

    public function testCanCreateACamelizedString() : void
    {
        $this->specify(
            'Can create a camelized syntax string',
            function () {
                $str = $this->_data()['camelize'];
                $result = $this->service->camelize($str);
                expect($result)->equals($this->_data()['camelizedResult']);
            }
        );
    }

    public function testCanCreateAVariablizedString() : void
    {
        $this->specify(
            'Can create a variabilized string',
            function () {
                $str = $this->_data()['variablize'];
                $result = $this->service->variablize($str);
                expect($result)->equals($this->_data()['variablizedResult']);
            }
        );
    }

    /**
     * Return test data
     */
    protected function _data() : array
    {
        return [
            'camelize' => 'This would - make_A nice-camel_case',
            'variablize' => 'VarNumber-String',
            'camelizedResult' => 'ThisWouldMakeANiceCamelCase',
            'variablizedResult' => 'varNumberString',
        ];
    }
}
