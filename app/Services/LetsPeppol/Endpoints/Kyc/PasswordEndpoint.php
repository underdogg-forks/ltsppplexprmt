<?php

namespace App\Services\LetsPeppol\Endpoints\Kyc;

use App\Services\LetsPeppol\Endpoints\BaseEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Password endpoint client (KycService)
 * 
 * Namespace: Kyc
 * Base URL: /api/password, /sapi/password
 */
class PasswordEndpoint extends BaseEndpoint
{
    /**
     * Request password reset
     * 
     * Request:
     * {
     *   "email": "user@example.com"
     * }
     * 
     * Response: No content (204)
     */
    public function forgot(string $email, ?string $language = null): void
    {
        $headers = [];
        if ($language) {
            $headers['Accept-Language'] = $language;
        }

        $this->request(
            RequestMethod::POST,
            '/api/password/forgot',
            ['email' => $email],
            [],
            $headers
        );
    }

    /**
     * Reset password with token
     * 
     * Request:
     * {
     *   "token": "reset-token-from-email",
     *   "newPassword": "newSecurePassword123"
     * }
     * 
     * Response: No content (204)
     */
    public function reset(string $token, string $newPassword): void
    {
        $this->request(
            RequestMethod::POST,
            '/api/password/reset',
            [
                'token' => $token,
                'newPassword' => $newPassword,
            ]
        );
    }

    /**
     * Change password (requires authentication)
     * 
     * Request:
     * {
     *   "oldPassword": "oldPassword123",
     *   "newPassword": "newSecurePassword123"
     * }
     * 
     * Response: No content (204)
     */
    public function change(string $oldPassword, string $newPassword): void
    {
        $this->request(
            RequestMethod::POST,
            '/sapi/password/change',
            [
                'oldPassword' => $oldPassword,
                'newPassword' => $newPassword,
            ]
        );
    }
}
