<?php

namespace App\Services\LetsPeppol\Endpoints\Kyc;

use App\Services\LetsPeppol\Endpoints\BaseEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Authentication endpoint client (KycService)
 * 
 * Namespace: Kyc
 * Base URL: /api/jwt/auth, /sapi/company
 */
class AuthenticationEndpoint extends BaseEndpoint
{
    /**
     * Authenticate and get JWT token
     * 
     * Request: Basic authentication (email:password encoded in base64)
     * 
     * Response:
     * "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
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
     * 
     * Response:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "name": "Company Name BVBA",
     *   "vatNumber": "BE0123456789",
     *   "email": "info@company.com",
     *   "registered": true
     * }
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
     * 
     * Response:
     * [
     *   {
     *     "peppolId": "0208:BE0123456789",
     *     "name": "Company Name BVBA",
     *     "vatNumber": "BE0123456789",
     *     "country": "BE"
     *   }
     * ]
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
     * 
     * Response:
     * {
     *   "token": "new-jwt-token",
     *   "status": "updated"
     * }
     * or
     * {
     *   "status": "already_registered"
     * }
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
     * 
     * Response:
     * {
     *   "token": "new-jwt-token",
     *   "status": "updated"
     * }
     * or
     * {
     *   "status": "already_unregistered"
     * }
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
     * 
     * Response: PDF binary content
     */
    public function getSignedContract(): string
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/company/signed-contract'
        );
    }
}
