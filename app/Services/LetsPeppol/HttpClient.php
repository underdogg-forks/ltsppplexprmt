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
        $url = ltrim($endpoint, '/');
        
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

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
            RequestMethod::GET => $http->get($url)->throw(),
            RequestMethod::POST => $isXmlContent 
                ? $http->withBody($data['body'] ?? '', 'text/xml')->post($url)->throw()
                : $http->post($url, $data)->throw(),
            RequestMethod::PUT => $isXmlContent 
                ? $http->withBody($data['body'] ?? '', 'text/xml')->put($url)->throw()
                : $http->put($url, $data)->throw(),
            RequestMethod::DELETE => $http->delete($url, $data)->throw(),
            RequestMethod::PATCH => $http->patch($url, $data)->throw(),
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
