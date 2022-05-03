<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Helper;

use Codeception\Module;

class Application extends Module
{
    /**
     * Return the bootstrapped Phalcon application
     */
    public function getApplication()
    {
        $service = $this->getModule('Phalcon5');

        return $service->getApplication();
    }

    /**
     * Return a service from the container
     */
    public function getService(string $service)
    {
        $di = $this->getApplication()->getDI();

        if (!$di->has($service)) {
            return null;
        }

        return $di->get($service);
    }
}
