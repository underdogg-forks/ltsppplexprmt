<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Endpoints\App\PartnersEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PartnersEndpointTest extends TestCase
{
    private ClientInterface $mockClient;
    private PartnersEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockClient = $this->createMock(ClientInterface::class);
        $this->endpoint = new PartnersEndpoint($this->mockClient);
    }

    #[Test]
    public function it_lists_all_partners(): void
    {
        // Arrange
        $expectedPartners = [
            ['id' => 1, 'name' => 'Partner 1'],
            ['id' => 2, 'name' => 'Partner 2'],
        ];

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::GET),
                $this->equalTo('/sapi/partner')
            )
            ->willReturn($expectedPartners);

        // Act
        $result = $this->endpoint->list();

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals('Partner 1', $result[0]['name']);
    }

    #[Test]
    public function it_searches_partners_by_peppol_id(): void
    {
        // Arrange
        $peppolId = '0208:BE0987654321';
        $expectedResults = [
            ['id' => 1, 'name' => 'Partner Company', 'peppolId' => $peppolId],
        ];

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::GET),
                $this->equalTo('/sapi/partner/search'),
                $this->equalTo([]),
                $this->equalTo(['peppolId' => $peppolId])
            )
            ->willReturn($expectedResults);

        // Act
        $result = $this->endpoint->search($peppolId);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($peppolId, $result[0]['peppolId']);
    }

    #[Test]
    public function it_creates_new_partner(): void
    {
        // Arrange
        $partnerData = [
            'peppolId' => '0208:BE0987654321',
            'name' => 'New Partner',
            'vatNumber' => 'BE0987654321',
        ];

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::POST),
                $this->equalTo('/sapi/partner'),
                $this->equalTo($partnerData)
            )
            ->willReturn(array_merge($partnerData, ['id' => 1]));

        // Act
        $result = $this->endpoint->create($partnerData);

        // Assert
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('New Partner', $result['name']);
    }

    #[Test]
    public function it_updates_existing_partner(): void
    {
        // Arrange
        $partnerId = 1;
        $partnerData = ['name' => 'Updated Partner'];

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::PUT),
                $this->equalTo("/sapi/partner/{$partnerId}"),
                $this->equalTo($partnerData)
            )
            ->willReturn(array_merge($partnerData, ['id' => $partnerId]));

        // Act
        $result = $this->endpoint->update($partnerId, $partnerData);

        // Assert
        $this->assertEquals('Updated Partner', $result['name']);
    }

    #[Test]
    public function it_deletes_partner_by_id(): void
    {
        // Arrange
        $partnerId = 1;

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::DELETE),
                $this->equalTo("/sapi/partner/{$partnerId}")
            )
            ->willReturn(null);

        // Act
        $this->endpoint->delete($partnerId);

        // Assert - if no exception thrown, test passes
        $this->assertTrue(true);
    }
}
