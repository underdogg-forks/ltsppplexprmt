<?php

namespace App\Services\LetsPeppol;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Decorators\HttpExceptionHandler;
use App\Services\LetsPeppol\Decorators\RequestLogger;
use App\Services\LetsPeppol\Endpoints\Proxy\DocumentsEndpoint;
use App\Services\LetsPeppol\Endpoints\Proxy\MonitorEndpoint;
use App\Services\LetsPeppol\Endpoints\Proxy\RegistryEndpoint;

/**
 * Proxy API Service Client
 * 
 * Provides access to document transmission and registry endpoints
 */
class ProxyService
{
    protected ClientInterface $client;
    protected DocumentsEndpoint $documents;
    protected RegistryEndpoint $registry;
    protected MonitorEndpoint $monitor;

    public function __construct(?string $baseUrl = null)
    {
        $baseUrl = $baseUrl ?? config('services.letspeppol.proxy_url', 'https://proxy.letspeppol.org');
        
        // Build decorator chain: RequestLogger → HttpExceptionHandler → HttpClient
        $this->client = new RequestLogger(
            new HttpExceptionHandler(
                new HttpClient($baseUrl)
            )
        );

        // Initialize endpoint clients
        $this->documents = new DocumentsEndpoint($this->client);
        $this->registry = new RegistryEndpoint($this->client);
        $this->monitor = new MonitorEndpoint($this->client);
    }

    /**
     * Get documents endpoint
     */
    public function documents(): DocumentsEndpoint
    {
        return $this->documents;
    }

    /**
     * Get registry endpoint
     */
    public function registry(): RegistryEndpoint
    {
        return $this->registry;
    }

    /**
     * Get monitor endpoint
     */
    public function monitor(): MonitorEndpoint
    {
        return $this->monitor;
    }

    /**
     * Set JWT token for all endpoints
     */
    public function setToken(string $token): static
    {
        $this->client->setToken($token);
        return $this;
    }

    /**
     * Get JWT token
     */
    public function getToken(): ?string
    {
        return $this->client->getToken();
    }
}
