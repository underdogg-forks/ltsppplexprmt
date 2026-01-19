<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Base class for all endpoint clients
 */
abstract class BaseEndpoint
{
    public function __construct(
        protected ClientInterface $client
    ) {
    }

    /**
     * Make a request using the configured client
     */
    protected function request(
        RequestMethod $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        array $headers = []
    ): mixed {
        return $this->client->request($method, $endpoint, $data, $queryParams, $headers);
    }
}
