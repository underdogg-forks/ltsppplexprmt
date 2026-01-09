<?php

namespace App\Services\LetsPeppol;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Enums\RequestMethod;
use Illuminate\Http\Client\PendingRequest;
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
        $http = $this->buildHttpClient($headers);
        $url = $this->buildUrl($endpoint, $queryParams);

        // Check if we're sending XML content
        $isXmlContent = isset($headers['Content-Type']) && $headers['Content-Type'] === 'text/xml';

        $response = match ($method) {
            RequestMethod::GET => $http->get($url),
            RequestMethod::POST => $isXmlContent 
                ? $http->withBody($data['body'] ?? '', 'text/xml')->post($url)
                : $http->post($url, $data),
            RequestMethod::PUT => $isXmlContent 
                ? $http->withBody($data['body'] ?? '', 'text/xml')->put($url)
                : $http->put($url, $data),
            RequestMethod::DELETE => $http->delete($url, $data),
            RequestMethod::PATCH => $http->patch($url, $data),
        };

        return $this->handleResponse($response);
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

    protected function buildHttpClient(array $additionalHeaders = []): PendingRequest
    {
        $http = Http::baseUrl($this->baseUrl)
            ->accept('application/json')
            ->timeout(30);

        if ($this->token) {
            $http->withToken($this->token);
        }

        foreach ($additionalHeaders as $key => $value) {
            $http->withHeaders([$key => $value]);
        }

        return $http;
    }

    protected function buildUrl(string $endpoint, array $queryParams = []): string
    {
        $url = ltrim($endpoint, '/');
        
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    protected function handleResponse($response): mixed
    {
        if ($response->successful()) {
            $contentType = $response->header('Content-Type');
            
            // Return raw body for non-JSON responses
            if ($contentType && !str_contains($contentType, 'application/json')) {
                return $response->body();
            }

            return $response->json();
        }

        throw new \RuntimeException(
            "API request failed: {$response->status()} - {$response->body()}",
            $response->status()
        );
    }
}
