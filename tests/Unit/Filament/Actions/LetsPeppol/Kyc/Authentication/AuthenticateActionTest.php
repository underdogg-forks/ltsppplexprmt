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
    
    #[Test]
    public function it_authenticates_user_successfully(): void
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'password123';
        $expectedToken = 'test-token-abc123';
        
        $this->fakeClient->queueResponse($expectedToken);
        
        $action = AuthenticateAction::make();
        
        // Act
        $action->call([
            'email' => $email,
            'password' => $password,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/kyc/authenticate');
        $this->fakeClient->assertRequestCount(1);
    }
    
    #[Test]
    public function it_handles_authentication_failure(): void
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'wrong-password';
        
        $this->fakeClient->queueException(new \Exception('Invalid credentials'));
        
        $action = AuthenticateAction::make();
        
        // Act & Assert
        try {
            $action->call([
                'email' => $email,
                'password' => $password,
            ]);
        } catch (\Exception $e) {
            $this->assertEquals('Invalid credentials', $e->getMessage());
        }
    }
}
