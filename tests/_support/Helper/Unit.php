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
use Codeception\Util\Debug;

class Unit extends Module
{
    /**
     * {@inheritDoc}
     */
    public function debug($mixed)
    {
        return Debug::debug($mixed);
    }
}
