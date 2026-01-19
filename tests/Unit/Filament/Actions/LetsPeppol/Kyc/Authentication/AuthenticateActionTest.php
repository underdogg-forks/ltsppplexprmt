<?php

namespace Tests\Unit\Filament\Actions\LetsPeppol\Kyc\Authentication;

use App\Filament\Actions\LetsPeppol\Kyc\Authentication\AuthenticateAction;
use App\Services\LetsPeppol\LetsPeppolClient;
use App\Services\LetsPeppol\Testing\FakeClient;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AuthenticateAction::class)]
class AuthenticateActionTest extends TestCase
{
    private FakeClient $fakeClient;
    private LetsPeppolClient $letsPeppolClient;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Arrange
        $this->fakeClient = new FakeClient();
        $this->letsPeppolClient = new LetsPeppolClient($this->fakeClient);
        
        App::instance(LetsPeppolClient::class, $this->letsPeppolClient);
    }
    
    /**
     * Test user authentication with valid credentials
     * 
     * API Endpoint: POST /kyc/authenticate
     * 
     * Request Payload:
     * {
     *   "email": "test@example.com",
     *   "password": "password123"
     * }
     * 
     * Expected Response:
     * "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." (JWT token string)
     * 
     * The token should be stored and used for subsequent API calls
     */
    #[Test]
    public function it_authenticates_user_with_valid_credentials(): void
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'SecurePassword123!';
        $expectedToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJ0ZXN0QGV4YW1wbGUuY29tIiwiZXhwIjoxNzM2NTI0ODAwfQ.test-signature';
        
        $this->fakeClient->queueResponse($expectedToken);
        
        $action = AuthenticateAction::make();
        
        // Act
        $result = $action->call([
            'email' => $email,
            'password' => $password,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/kyc/authenticate');
        $this->fakeClient->assertRequestCount(1);
        
        // Verify token is returned and valid format
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringStartsWith('eyJ', $result, 'JWT token should start with eyJ');
        $this->assertGreaterThan(50, strlen($result), 'JWT token should be sufficiently long');
        
        // Verify token structure (header.payload.signature)
        $tokenParts = explode('.', $result);
        $this->assertCount(3, $tokenParts, 'JWT should have 3 parts separated by dots');
    }
    
    /**
     * Test authentication failure with invalid credentials
     * 
     * API Endpoint: POST /kyc/authenticate
     * 
     * Request Payload:
     * {
     *   "email": "test@example.com",
     *   "password": "wrong-password"
     * }
     * 
     * Expected: AuthenticationException with "Invalid credentials" message
     * Status Code: 401 Unauthorized
     */
    #[Test]
    public function it_rejects_invalid_credentials(): void
    {
        // Arrange
        $email = 'test@example.com';
        $wrongPassword = 'WrongPassword123';
        
        $this->fakeClient->queueException(
            new \App\Services\LetsPeppol\Exceptions\AuthenticationException(
                'Invalid credentials: Email or password is incorrect'
            )
        );
        
        $action = AuthenticateAction::make();
        
        // Act & Assert
        $this->expectException(\App\Services\LetsPeppol\Exceptions\AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');
        
        $action->call([
            'email' => $email,
            'password' => $wrongPassword,
        ]);
        
        // Verify the request was made
        $this->fakeClient->assertRequestSent('/kyc/authenticate');
    }
    
    /**
     * Test authentication with non-existent user
     * 
     * API Endpoint: POST /kyc/authenticate
     * 
     * Request Payload:
     * {
     *   "email": "nonexistent@example.com",
     *   "password": "somePassword"
     * }
     * 
     * Expected: AuthenticationException with "User not found" message
     */
    #[Test]
    public function it_handles_non_existent_user(): void
    {
        // Arrange
        $nonExistentEmail = 'nonexistent@example.com';
        $password = 'SomePassword123';
        
        $this->fakeClient->queueException(
            new \App\Services\LetsPeppol\Exceptions\AuthenticationException(
                'User not found'
            )
        );
        
        $action = AuthenticateAction::make();
        
        // Act & Assert
        $this->expectException(\App\Services\LetsPeppol\Exceptions\AuthenticationException::class);
        $this->expectExceptionMessage('User not found');
        
        $action->call([
            'email' => $nonExistentEmail,
            'password' => $password,
        ]);
    }
    
    /**
     * Test authentication with malformed email
     * 
     * API Endpoint: POST /kyc/authenticate
     * 
     * Scenario: Email doesn't match valid email format
     * Expected: ValidationException before API call
     */
    #[Test]
    public function it_validates_email_format_before_api_call(): void
    {
        // Arrange
        $invalidEmail = 'not-an-email';
        $password = 'Password123';
        
        $this->fakeClient->queueException(
            new \App\Services\LetsPeppol\Exceptions\ValidationException(
                'Invalid email format'
            )
        );
        
        $action = AuthenticateAction::make();
        
        // Act & Assert
        $this->expectException(\App\Services\LetsPeppol\Exceptions\ValidationException::class);
        
        $action->call([
            'email' => $invalidEmail,
            'password' => $password,
        ]);
    }
}
