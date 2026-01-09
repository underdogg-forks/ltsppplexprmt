<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\App\PartnersEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PartnersEndpoint::class)]
class PartnersEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private PartnersEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new PartnersEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_lists_all_partners(): void
    {
        // Arrange
        $expectedPartners = [
            ['id' => 1, 'name' => 'Partner 1'],
            ['id' => 2, 'name' => 'Partner 2'],
        ];
        $this->fakeClient->queueResponse($expectedPartners);

        // Act
        $result = $this->endpoint->list();

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals('Partner 1', $result[0]['name']);
        $this->fakeClient->assertRequestSent('/sapi/partner', RequestMethod::GET);
    }

    #[Test]
    public function it_searches_partners_by_peppol_id(): void
    {
        // Arrange
        $peppolId = '0208:BE0987654321';
        $expectedResults = [
            ['id' => 1, 'name' => 'Partner Company', 'peppolId' => $peppolId],
        ];
        $this->fakeClient->queueResponse($expectedResults);

        // Act
        $result = $this->endpoint->search($peppolId);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($peppolId, $result[0]['peppolId']);
        $this->fakeClient->assertRequestSent('/sapi/partner/search', RequestMethod::GET);
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
        $this->fakeClient->queueResponse(array_merge($partnerData, ['id' => 1]));

        // Act
        $result = $this->endpoint->create($partnerData);

        // Assert
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('New Partner', $result['name']);
        $this->fakeClient->assertRequestSentWithData('/sapi/partner', $partnerData, RequestMethod::POST);
    }

    #[Test]
    public function it_updates_existing_partner(): void
    {
        // Arrange
        $partnerId = 1;
        $partnerData = ['name' => 'Updated Partner'];
        $this->fakeClient->queueResponse(array_merge($partnerData, ['id' => $partnerId]));

        // Act
        $result = $this->endpoint->update($partnerId, $partnerData);

        // Assert
        $this->assertEquals('Updated Partner', $result['name']);
        $this->fakeClient->assertRequestSentWithData("/sapi/partner/{$partnerId}", $partnerData, RequestMethod::PUT);
    }

    #[Test]
    public function it_deletes_partner_by_id(): void
    {
        // Arrange
        $partnerId = 1;
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->delete($partnerId);

        // Assert
        $this->fakeClient->assertRequestSent("/sapi/partner/{$partnerId}", RequestMethod::DELETE);
    }
}

