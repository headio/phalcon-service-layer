<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Helper;

use Codeception\Util\Debug;
use Codeception\Module;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class Integration extends Module
{
    /**
     * {@inheritDoc}
     */
    public function debug($mixed)
    {
        return Debug::debug($mixed);
    }

    /**
     * Get inaccessible class property
     */
    public function getClassProperty(string $class, string $name): ?ReflectionProperty
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
     *
     * @param object
     * @param string
     */
    public function getClassMethod($class, string $method): ?ReflectionMethod
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
