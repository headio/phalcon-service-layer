<?php
/**
 * This source file is subject to the MIT License.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this package.
 */
declare(strict_types=1);

namespace Headio\Phalcon\ServiceLayer\Exception;

use Throwable;

class HttpException extends RuntimeException implements ExceptionInterface, HttpExceptionInterface
{
    private int $statusCode;

    private array $headers;

    public function __construct(
        int $statusCode,
        string $message = '',
        int $code = 0,
        Throwable $throwable = null,
        array $headers = []
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($message, $code, $throwable);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(array $headers): HttpExceptionInterface
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
