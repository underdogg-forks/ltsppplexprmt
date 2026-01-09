<?php

namespace App\Services\LetsPeppol\Endpoints\Kyc;

use App\Services\LetsPeppol\Endpoints\BaseEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Registration endpoint client (KycService)
 * 
 * Namespace: Kyc
 * Base URL: /api/register, /api/identity
 */
class RegistrationEndpoint extends BaseEndpoint
{
    /**
     * Get company information by Peppol ID (Registration step 1)
     * 
     * Response:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "name": "Company Name BVBA",
     *   "vatNumber": "BE0123456789",
     *   "street": "Main Street 1",
     *   "city": "Brussels",
     *   "postalCode": "1000",
     *   "country": "BE"
     * }
     */
    public function getCompany(string $peppolId): array
    {
        return $this->request(
            RequestMethod::GET,
            "/api/register/company/{$peppolId}"
        );
    }

    /**
     * Confirm company and send verification email (Registration step 2)
     * 
     * Request:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "email": "admin@company.com",
     *   "name": "John Doe",
     *   "password": "securePassword123"
     * }
     * 
     * Response:
     * {
     *   "status": "email_sent",
     *   "email": "admin@company.com"
     * }
     */
    public function confirmCompany(array $data, ?string $language = null): array
    {
        $headers = [];
        if ($language) {
            $headers['Accept-Language'] = $language;
        }

        return $this->request(
            RequestMethod::POST,
            '/api/register/confirm-company',
            $data,
            [],
            $headers
        );
    }

    /**
     * Verify email token (Registration step 3)
     * 
     * Request:
     * {
     *   "token": "verification-token-from-email"
     * }
     * 
     * Response:
     * {
     *   "token": "session-token",
     *   "directors": [
     *     {
     *       "id": 1,
     *       "name": "John Doe",
     *       "nationalNumber": "12345678901"
     *     }
     *   ]
     * }
     */
    public function verifyToken(string $token): array
    {
        return $this->request(
            RequestMethod::POST,
            '/api/register/verify',
            ['token' => $token]
        );
    }

    /**
     * Prepare document for signing (Registration step 4)
     * 
     * Request:
     * {
     *   "directorId": 1,
     *   "token": "session-token"
     * }
     * 
     * Response:
     * {
     *   "signingToken": "signing-token",
     *   "documentHash": "hash",
     *   "status": "ready"
     * }
     */
    public function prepareSigning(array $data): array
    {
        return $this->request(
            RequestMethod::POST,
            '/api/identity/sign/prepare',
            $data
        );
    }

    /**
     * Get contract PDF (Registration step 5)
     * 
     * Response: PDF binary content
     */
    public function getContract(int $directorId, string $token): string
    {
        return $this->request(
            RequestMethod::GET,
            "/api/identity/contract/{$directorId}",
            [],
            ['token' => $token]
        );
    }

    /**
     * Finalize document signing (Registration step 6)
     * 
     * Request:
     * {
     *   "signature": "base64-encoded-signature",
     *   "signingToken": "signing-token"
     * }
     * 
     * Response:
     * {
     *   "pdf": "base64-encoded-signed-pdf",
     *   "status": "completed",
     *   "provider": "eid"
     * }
     */
    public function finalizeSigning(array $data): array
    {
        // Note: The response includes special headers that need to be extracted
        // This is handled by the HttpClient
        $result = $this->request(
            RequestMethod::POST,
            '/api/identity/sign/finalize',
            $data
        );

        return [
            'pdf' => $result,
            'status' => 'completed',
            'provider' => 'default',
        ];
    }
}
