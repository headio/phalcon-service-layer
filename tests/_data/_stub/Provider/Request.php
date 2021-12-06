<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\Provider;

use Phalcon\Http\Request as Service;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class Request implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'request',
            function () {
                return new Service();
            }
        );
    }
}
