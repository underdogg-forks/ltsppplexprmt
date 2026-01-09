<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Password endpoint client
 */
class PasswordEndpoint extends BaseEndpoint
{
    /**
     * Request password reset
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
