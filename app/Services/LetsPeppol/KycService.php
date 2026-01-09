<?php

namespace App\Services\LetsPeppol;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Decorators\HttpExceptionHandler;
use App\Services\LetsPeppol\Decorators\RequestLogger;
use App\Services\LetsPeppol\Endpoints\AuthenticationEndpoint;
use App\Services\LetsPeppol\Endpoints\PasswordEndpoint;
use App\Services\LetsPeppol\Endpoints\RegistrationEndpoint;

/**
 * KYC API Service Client
 * 
 * Provides access to authentication and registration endpoints
 */
class KycService
{
    protected ClientInterface $client;
    protected AuthenticationEndpoint $authentication;
    protected RegistrationEndpoint $registration;
    protected PasswordEndpoint $password;

    public function __construct(?string $baseUrl = null)
    {
        $baseUrl = $baseUrl ?? config('services.letspeppol.kyc_url', 'https://kyc.letspeppol.org');
        
        // Build decorator chain: RequestLogger → HttpExceptionHandler → HttpClient
        $this->client = new RequestLogger(
            new HttpExceptionHandler(
                new HttpClient($baseUrl)
            )
        );

        // Initialize endpoint clients
        $this->authentication = new AuthenticationEndpoint($this->client);
        $this->registration = new RegistrationEndpoint($this->client);
        $this->password = new PasswordEndpoint($this->client);
    }

    /**
     * Get authentication endpoint
     */
    public function authentication(): AuthenticationEndpoint
    {
        return $this->authentication;
    }

    /**
     * Get registration endpoint
     */
    public function registration(): RegistrationEndpoint
    {
        return $this->registration;
    }

    /**
     * Get password endpoint
     */
    public function password(): PasswordEndpoint
    {
        return $this->password;
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

    /**
     * Authenticate and set token
     */
    public function authenticate(string $email, string $password): string
    {
        $token = $this->authentication->authenticate($email, $password);
        $this->setToken($token);
        return $token;
    }
}
