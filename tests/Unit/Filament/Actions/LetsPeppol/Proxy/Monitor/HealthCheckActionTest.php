<?php

namespace Tests\Unit\Filament\Actions\LetsPeppol\Proxy\Monitor;

use App\Filament\Actions\LetsPeppol\Proxy\Monitor\HealthCheckAction;
use App\Services\LetsPeppol\LetsPeppolClient;
use App\Services\LetsPeppol\Testing\FakeClient;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(HealthCheckAction::class)]
class HealthCheckActionTest extends TestCase
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
    public function it_performs_health_check_successfully(): void
    {
        // Arrange
        $this->fakeClient->queueResponse('OK');
        
        $action = HealthCheckAction::make();
        
        // Act
        $action->call();
        
        // Assert
        $this->fakeClient->assertRequestSent('/proxy/ping');
        $this->fakeClient->assertRequestCount(1);
    }
}
