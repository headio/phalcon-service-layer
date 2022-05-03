<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Provider;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class Db implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'db',
            function () {
                $config = $this->get('config');
                $adapter = 'Phalcon\\Db\\Adapter\\Pdo\\' . $config->database->adapter;
                $service = new $adapter($config->database->toArray());

                return $service;
            }
        );
    }
}
