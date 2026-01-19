<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\App\PeppolDirectoryEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PeppolDirectoryEndpoint::class)]
class PeppolDirectoryEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private PeppolDirectoryEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new PeppolDirectoryEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_searches_peppol_directory_by_name(): void
    {
        // Arrange
        $name = 'Company';
        $expectedResults = [
            [
                'peppolId' => '0208:BE0123456789',
                'name' => 'Company Name BVBA',
                'country' => 'BE',
            ],
        ];
        $this->fakeClient->queueResponse($expectedResults);

        // Act
        $result = $this->endpoint->search($name);

        // Assert
        $this->assertEquals($expectedResults, $result);
        $this->fakeClient->assertRequestSent('/sapi/peppol-directory/search', RequestMethod::GET);
    }

    #[Test]
    public function it_searches_peppol_directory_by_participant_id(): void
    {
        // Arrange
        $participantId = '0208:BE0123456789';
        $expectedResults = [
            [
                'peppolId' => $participantId,
                'name' => 'Company Name BVBA',
            ],
        ];
        $this->fakeClient->queueResponse($expectedResults);

        // Act
        $result = $this->endpoint->search(null, $participantId);

        // Assert
        $this->assertEquals($expectedResults, $result);
        $this->fakeClient->assertRequestSent('/sapi/peppol-directory/search', RequestMethod::GET);
    }

    #[Test]
    public function it_searches_peppol_directory_with_both_parameters(): void
    {
        // Arrange
        $name = 'Company';
        $participantId = '0208:BE0123456789';
        $this->fakeClient->queueResponse([]);

        // Act
        $this->endpoint->search($name, $participantId);

        // Assert
        $this->fakeClient->assertRequestSent('/sapi/peppol-directory/search', RequestMethod::GET);
        $this->fakeClient->assertRequestCount(1);
    }
}
