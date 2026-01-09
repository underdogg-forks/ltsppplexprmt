<?php

namespace App\Services\LetsPeppol\Contracts;

use App\Services\LetsPeppol\Enums\RequestMethod;

interface ClientInterface
{
    /**
     * Make an API request
     */
    public function request(
        RequestMethod $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        array $headers = []
    ): mixed;

    /**
     * Set the authentication token
     */
    public function setToken(string $token): static;

    /**
     * Get the current authentication token
     */
    public function getToken(): ?string;
}
