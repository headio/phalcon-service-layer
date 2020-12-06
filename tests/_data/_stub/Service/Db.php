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

namespace Stub\Service;

use Phalcon\Db\AdapterInterface;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\EventInterface;

class Db implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di) : void
    {
        $di->setShared(
            'db',
            function () use ($di) {
                $config = $this->get('config');
                $adapter = 'Phalcon\\Db\\Adapter\\Pdo\\' . $config->database->adapter;
                $service = new $adapter($config->database->toArray());

                return $service;
            }
        );
    }
}
