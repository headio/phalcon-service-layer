<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Stub\View;

use Phalcon\DI\Injectable;

/**
 * Volt functions and filters
 */
class VoltExtension extends Injectable
{
    /**
     * Compile filters for volt.
     *
     * @return mixed
     */
    public function compileFilter(string $name, string $args)
    {
        switch ($name) {
            case 't':
                return '$this->i18n->query(' . $args . ')';
            case 'intlDateFormatter':
                return '$dateFormatter(' . $args . ')';
            default:
                return;
        }
    }
}
