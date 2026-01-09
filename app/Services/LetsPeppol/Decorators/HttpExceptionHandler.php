<?php

namespace App\Services\LetsPeppol\Decorators;

use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Exceptions\ApiException;
use App\Services\LetsPeppol\Exceptions\AuthenticationException;
use App\Services\LetsPeppol\Exceptions\NotFoundException;
use App\Services\LetsPeppol\Exceptions\ValidationException;

/**
 * HTTP exception handler decorator
 */
class HttpExceptionHandler extends ClientDecorator
{
    public function request(
        RequestMethod $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        array $headers = []
    ): mixed {
        try {
            return parent::request($method, $endpoint, $data, $queryParams, $headers);
        } catch (\RuntimeException $e) {
            $this->handleException($e);
        }
    }

    protected function handleException(\RuntimeException $e): never
    {
        $statusCode = $e->getCode();
        $message = $e->getMessage();

        throw match (true) {
            $statusCode === 401 => new AuthenticationException($message, $statusCode, $e),
            $statusCode === 404 => new NotFoundException($message, $statusCode, $e),
            $statusCode === 422 => new ValidationException($message, $statusCode, $e),
            default => new ApiException($message, $statusCode, $e),
        };
    }
}
