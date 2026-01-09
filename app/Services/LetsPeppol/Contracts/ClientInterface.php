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
}
