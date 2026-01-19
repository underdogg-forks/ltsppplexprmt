<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\Proxy\RegistryEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(RegistryEndpoint::class)]
class RegistryEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private RegistryEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new RegistryEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_retrieves_registry_information(): void
    {
        // Arrange
        $expectedRegistry = [
            'peppolId' => '0208:BE0123456789',
            'registered' => true,
            'status' => 'ACTIVE',
            'registeredAt' => '2024-01-01T00:00:00Z',
        ];
        $this->fakeClient->queueResponse($expectedRegistry);

        // Act
        $result = $this->endpoint->get();

        // Assert
        $this->assertEquals($expectedRegistry, $result);
        $this->fakeClient->assertRequestSent('/sapi/registry', RequestMethod::GET);
    }

    #[Test]
    public function it_registers_on_access_point(): void
    {
        // Arrange
        $registrationData = [
            'peppolId' => '0208:BE0123456789',
            'documentTypes' => ['urn:oasis:names:specification:ubl:schema:xsd:Invoice-2'],
        ];
        $expectedResponse = [
            'peppolId' => '0208:BE0123456789',
            'registered' => true,
            'status' => 'ACTIVE',
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->register($registrationData);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent('/sapi/registry', RequestMethod::POST);
        $this->fakeClient->assertRequestSentWithData($registrationData);
    }

    #[Test]
    public function it_unregisters_from_access_point(): void
    {
        // Arrange
        $expectedResponse = [
            'peppolId' => '0208:BE0123456789',
            'registered' => false,
            'status' => 'UNREGISTERED',
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->unregister();

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent('/sapi/registry/unregister', RequestMethod::PUT);
    }

    #[Test]
    public function it_deletes_registry_entry(): void
    {
        // Arrange
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->delete();

        // Assert
        $this->fakeClient->assertRequestSent('/sapi/registry', RequestMethod::DELETE);
        $this->fakeClient->assertRequestCount(1);
    }
}
