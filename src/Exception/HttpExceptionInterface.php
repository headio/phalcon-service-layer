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

namespace Headio\Phalcon\ServiceLayer\Exception;

interface HttpExceptionInterface
{
    /**
     * Return the response headers
     */
    public function getHeaders() : array;

    /**
     * Set the response headers
     */
    public function setHeaders(array $headers) : HttpExceptionInterface;

    /**
     * Return the http status code
     */
    public function getStatusCode() : int;
}
