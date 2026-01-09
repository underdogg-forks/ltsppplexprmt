<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Authentication endpoint client
 */
class AuthenticationEndpoint extends BaseEndpoint
{
    /**
     * Authenticate and get JWT token
     */
    public function authenticate(string $email, string $password): string
    {
        $credentials = base64_encode("{$email}:{$password}");
        
        $result = $this->request(
            RequestMethod::POST,
            '/api/jwt/auth',
            [],
            [],
            ['Authorization' => "Basic {$credentials}"]
        );

        // Authentication returns the token as a string
        return $result;
    }

    /**
     * Get account information (requires JWT token)
     */
    public function getAccountInfo(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/company'
        );
    }

    /**
     * Search companies
     */
    public function searchCompanies(?string $vatNumber = null, ?string $peppolId = null, ?string $companyName = null): array
    {
        $params = array_filter([
            'vatNumber' => $vatNumber,
            'peppolId' => $peppolId,
            'companyName' => $companyName,
        ]);

        return $this->request(
            RequestMethod::GET,
            '/sapi/company/search',
            [],
            $params
        );
    }

    /**
     * Register on Peppol Directory
     */
    public function registerPeppol(): array
    {
        $result = $this->request(
            RequestMethod::POST,
            '/sapi/company/peppol/register'
        );

        // Handle both token update (200) and already registered (other success codes)
        if (is_string($result)) {
            return ['token' => $result, 'status' => 'updated'];
        }

        return ['status' => 'already_registered'];
    }

    /**
     * Unregister from Peppol Directory
     */
    public function unregisterPeppol(): array
    {
        $result = $this->request(
            RequestMethod::POST,
            '/sapi/company/peppol/unregister'
        );

        // Handle both token update (200) and already unregistered (other success codes)
        if (is_string($result)) {
            return ['token' => $result, 'status' => 'updated'];
        }

        return ['status' => 'already_unregistered'];
    }

    /**
     * Download signed contract
     */
    public function getSignedContract(): string
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/company/signed-contract'
        );
    }
}
