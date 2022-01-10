<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Module\Admin;

use Phalcon\Di\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    /**
     * {@inheritDoc}
     */
    public function registerAutoloaders(DiInterface $container = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function registerServices(DiInterface $container)
    {
        $config = $container->get('config');

        // web app module environment settings
        if (!$config->cli) {
            $container
                ->get('dispatcher')
                ->setDefaultNamespace(__NAMESPACE__ . '\\Controller')
            ;

            $container->get('view')->setViewsDir(__DIR__ . '/View/');
        }
    }
}
