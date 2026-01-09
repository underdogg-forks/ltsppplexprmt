<?php

namespace App\Services\LetsPeppol;

/**
 * Unified LetsPeppol API Client
 * 
 * Provides access to all LetsPeppol API modules:
 * - KYC: Authentication and registration
 * - Proxy: Document transmission and registry
 * - App: Document management and business logic
 */
class LetsPeppolClient
{
    protected KycService $kycService;
    protected ProxyService $proxyService;
    protected AppService $appService;

    public function __construct(
        ?string $kycUrl = null,
        ?string $proxyUrl = null,
        ?string $appUrl = null
    ) {
        $this->kycService = new KycService($kycUrl);
        $this->proxyService = new ProxyService($proxyUrl);
        $this->appService = new AppService($appUrl);
    }

    /**
     * Get KYC service
     */
    public function kyc(): KycService
    {
        return $this->kycService;
    }

    /**
     * Get Proxy service
     */
    public function proxy(): ProxyService
    {
        return $this->proxyService;
    }

    /**
     * Get App service
     */
    public function app(): AppService
    {
        return $this->appService;
    }

    /**
     * Set JWT token for all services
     */
    public function setToken(string $token): static
    {
        $this->kycService->setToken($token);
        $this->proxyService->setToken($token);
        $this->appService->setToken($token);
        return $this;
    }

    /**
     * Authenticate and set token for all services
     */
    public function authenticate(string $email, string $password): string
    {
        $token = $this->kycService->authenticate($email, $password);
        $this->setToken($token);
        return $token;
    }

    /**
     * Create a new instance with a specific token
     */
    public static function withToken(string $token): static
    {
        $client = new static();
        $client->setToken($token);
        return $client;
    }
}
