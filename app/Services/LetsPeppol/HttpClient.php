<?php

namespace App\Services\LetsPeppol;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Enums\RequestMethod;
use Illuminate\Support\Facades\Http;

/**
 * Base HTTP client for LetsPeppol API
 */
class HttpClient implements ClientInterface
{
    protected string $baseUrl;
    protected ?string $token = null;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function request(
        RequestMethod $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        array $headers = []
    ): mixed {
        $http = Http::baseUrl($this->baseUrl)
            ->accept('application/json')
            ->timeout(30);

        if ($this->token) {
            $http->withToken($this->token);
        }

        if (!empty($headers)) {
            $http->withHeaders($headers);
        }

        // Check if we're sending XML content
        $isXmlContent = isset($headers['Content-Type']) && $headers['Content-Type'] === 'text/xml';

        $response = match ($method) {
            RequestMethod::GET => $http->get($endpoint, $queryParams)->throw(),
            RequestMethod::POST => $isXmlContent 
                ? $http->withBody($data['body'] ?? '', 'text/xml')->post($endpoint, $queryParams)->throw()
                : $http->post($endpoint, array_merge($data, $queryParams))->throw(),
            RequestMethod::PUT => $isXmlContent 
                ? $http->withBody($data['body'] ?? '', 'text/xml')->put($endpoint, $queryParams)->throw()
                : $http->put($endpoint, array_merge($data, $queryParams))->throw(),
            RequestMethod::DELETE => $http->delete($endpoint, array_merge($data, $queryParams))->throw(),
            RequestMethod::PATCH => $http->patch($endpoint, array_merge($data, $queryParams))->throw(),
        };

        $contentType = $response->header('Content-Type');
            
        // Return raw body for non-JSON responses
        if ($contentType && !str_contains($contentType, 'application/json')) {
            return $response->body();
        }

        return $response->json();
    }

    public function setToken(string $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
}
