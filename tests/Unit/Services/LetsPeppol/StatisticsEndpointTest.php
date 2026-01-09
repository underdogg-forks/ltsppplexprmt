<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\App\StatisticsEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(StatisticsEndpoint::class)]
class StatisticsEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private StatisticsEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new StatisticsEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_retrieves_donation_statistics(): void
    {
        // Arrange
        $expectedStats = [
            'totalDonations' => 100.00,
            'currency' => 'EUR',
            'donationCount' => 10,
        ];
        $this->fakeClient->queueResponse($expectedStats);

        // Act
        $result = $this->endpoint->getDonation();

        // Assert
        $this->assertEquals($expectedStats, $result);
        $this->fakeClient->assertRequestSent('/sapi/statistics/donation', RequestMethod::GET);
    }

    #[Test]
    public function it_retrieves_account_statistics(): void
    {
        // Arrange
        $expectedStats = [
            'totalInvoices' => 150,
            'totalAmount' => 50000.00,
            'currency' => 'EUR',
        ];
        $this->fakeClient->queueResponse($expectedStats);

        // Act
        $result = $this->endpoint->getAccount();

        // Assert
        $this->assertEquals($expectedStats, $result);
        $this->fakeClient->assertRequestSent('/sapi/statistics/account', RequestMethod::GET);
    }
}
