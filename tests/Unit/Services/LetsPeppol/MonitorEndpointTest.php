<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\Proxy\MonitorEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MonitorEndpoint::class)]
class MonitorEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private MonitorEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new MonitorEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_performs_health_check(): void
    {
        // Arrange
        $this->fakeClient->queueResponse('OK');

        // Act
        $result = $this->endpoint->healthCheck();

        // Assert
        $this->assertEquals('OK', $result);
        $this->fakeClient->assertRequestSent('/api/monitor', RequestMethod::GET);
    }

    #[Test]
    public function it_tops_up_balance_for_testing(): void
    {
        // Arrange
        $amount = 100;
        $expectedResponse = "Balance topped up with {$amount}";
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->topUpBalance($amount);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent("/api/monitor/{$amount}", RequestMethod::GET);
    }
}
