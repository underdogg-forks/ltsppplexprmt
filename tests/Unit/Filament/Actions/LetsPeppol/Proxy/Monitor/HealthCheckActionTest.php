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
    
    /**
     * Test API health check with successful response
     * 
     * API Endpoint: GET /proxy/ping
     * 
     * Expected Response: "OK" (string)
     * 
     * This endpoint is used to verify:
     * - API service is running and accessible
     * - Network connectivity is working
     * - Authentication (if required) is valid
     * - System is ready to process documents
     */
    #[Test]
    public function it_confirms_api_service_is_healthy(): void
    {
        // Arrange
        $expectedResponse = 'OK';
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = HealthCheckAction::make();
        
        // Act
        $result = $action->call();
        
        // Assert
        $this->fakeClient->assertRequestSent('/proxy/ping');
        $this->fakeClient->assertRequestCount(1);
        
        // Verify health check response
        $this->assertIsString($result);
        $this->assertEquals('OK', $result);
        $this->assertNotEmpty($result, 'Health check should return non-empty response');
    }
    
    /**
     * Test API health check when service is down
     * 
     * API Endpoint: GET /proxy/ping
     * 
     * Scenario: API service is unavailable or experiencing issues
     * Expected: Exception indicating service unavailability
     * 
     * This helps administrators:
     * - Detect outages quickly
     * - Monitor uptime for 100s of users
     * - Trigger alerts for critical failures
     */
    #[Test]
    public function it_detects_service_unavailability(): void
    {
        // Arrange
        $this->fakeClient->queueException(
            new \RuntimeException('Service Unavailable: The API endpoint is not responding')
        );
        
        $action = HealthCheckAction::make();
        
        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Service Unavailable');
        
        $action->call();
        
        $this->fakeClient->assertRequestSent('/proxy/ping');
    }
    
    /**
     * Test API health check with timeout
     * 
     * API Endpoint: GET /proxy/ping
     * 
     * Scenario: API is slow or hanging, causing timeout
     * Expected: Exception indicating timeout occurred
     * 
     * Critical for production: Users need to know when API is degraded
     */
    #[Test]
    public function it_handles_timeout_gracefully(): void
    {
        // Arrange
        $this->fakeClient->queueException(
            new \RuntimeException('Request timeout: API did not respond within 30 seconds')
        );
        
        $action = HealthCheckAction::make();
        
        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('timeout');
        
        $action->call();
    }
}
