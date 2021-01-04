<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */

namespace Headio\Phalcon\ServiceLayer\Exception;

use Throwable;

class NotFoundException extends HttpException
{
    public function __construct(string $message = null, ?int $code = 0, Throwable $throwable = null, array $headers = [])
    {
        parent::__construct(404, $message, $code, $throwable, $headers);
    }
}
