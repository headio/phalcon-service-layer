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

namespace Headio\Phalcon\DomainLayer\Exception;

use Throwable;

class HttpException extends RuntimeException implements ExceptionInterface, HttpExceptionInterface
{
    /**
     * @var integer
     */
    private $statusCode;

    /**
     * @var array
     */
    private $headers;

    public function __construct(
        int $statusCode,
        string $message = null,
        int $code = 0,
        Throwable $throwable = null,
        array $headers = []) 
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($message, $code, $throwable);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(array $headers) : HttpExceptionInterface
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }
}
