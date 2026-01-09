<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Registration endpoint client
 */
class RegistrationEndpoint extends BaseEndpoint
{
    /**
     * Get company information by Peppol ID (Registration step 1)
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
