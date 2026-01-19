<?php

namespace App\Services\LetsPeppol\Decorators;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Base decorator for API clients
 */
abstract class ClientDecorator implements ClientInterface
{
    public function __construct(
        protected ClientInterface $client
    ) {
    }

    public function request(
        RequestMethod $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        array $headers = []
    ): mixed {
        return $this->client->request($method, $endpoint, $data, $queryParams, $headers);
    }

    public function setToken(string $token): static
    {
        $this->client->setToken($token);
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->client->getToken();
    }
}
