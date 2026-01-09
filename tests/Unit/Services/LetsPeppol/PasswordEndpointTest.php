<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\Kyc\PasswordEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordEndpoint::class)]
class PasswordEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private PasswordEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new PasswordEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_requests_password_reset(): void
    {
        // Arrange
        $email = 'user@example.com';
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->forgot($email);

        // Assert
        $this->fakeClient->assertRequestSent('/api/password/forgot', RequestMethod::POST);
        $this->fakeClient->assertRequestSentWithData(['email' => $email]);
    }

    #[Test]
    public function it_requests_password_reset_with_language(): void
    {
        // Arrange
        $email = 'user@example.com';
        $language = 'nl';
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->forgot($email, $language);

        // Assert
        $this->fakeClient->assertRequestSent('/api/password/forgot', RequestMethod::POST);
        $this->fakeClient->assertRequestCount(1);
    }

    #[Test]
    public function it_resets_password_with_token(): void
    {
        // Arrange
        $token = 'reset-token-123';
        $newPassword = 'newSecurePass123';
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->reset($token, $newPassword);

        // Assert
        $this->fakeClient->assertRequestSent('/api/password/reset', RequestMethod::POST);
        $this->fakeClient->assertRequestSentWithData([
            'token' => $token,
            'newPassword' => $newPassword,
        ]);
    }

    #[Test]
    public function it_changes_password_when_authenticated(): void
    {
        // Arrange
        $oldPassword = 'oldPass123';
        $newPassword = 'newSecurePass123';
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->change($oldPassword, $newPassword);

        // Assert
        $this->fakeClient->assertRequestSent('/sapi/password/change', RequestMethod::POST);
        $this->fakeClient->assertRequestSentWithData([
            'oldPassword' => $oldPassword,
            'newPassword' => $newPassword,
        ]);
    }
}
