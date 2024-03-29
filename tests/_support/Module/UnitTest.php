<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Module;

use Codeception\Specify;
use Codeception\Test\Unit;
use Phalcon\Di\DiInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use UnitTester;

class UnitTest extends Unit
{
    use Specify;

    protected UnitTester $tester;

    protected ?DiInterface $di = null;

    /**
     * {@inheritDoc}
     */
    protected function _before(): void
    {
        $this->di = $this->tester->getApplication()->getDI();
    }

    /**
     * {@inheritDoc}
     */
    protected function _after(): void
    {
        $this->di = null;
    }

    /**
     * Get inaccessible class property
     */
    protected function getClassProperty(string $class, string $name): ?ReflectionProperty
    {
        $rc = new ReflectionClass($class);

        while (!$rc->hasProperty($name)) {
            $rc = $rc->getParentClass();
        }

        if ($rc->hasProperty($name)) {
            $prop = $rc->getProperty($name);
            $prop->setAccessible(true);
            return $prop;
        }

        return null;
    }

    /**
     * Get inaccessible class method
     */
    protected function getClassMethod(string $class, string $method): ?ReflectionMethod
    {
        $rc = new ReflectionClass($class);

        while (!$rc->hasMethod($method)) {
            $rc = $rc->getParentClass();
        }

        if ($rc->hasMethod($method)) {
            $method = $rc->getMethod($method);
            $method->setAccessible(true);
            return $method;
        }

        return null;
    }
}
