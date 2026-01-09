<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\App\CompanyEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CompanyEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private CompanyEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new CompanyEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_retrieves_company_information(): void
    {
        // Arrange
        $expectedCompany = [
            'peppolId' => '0208:BE0123456789',
            'name' => 'Test Company BVBA',
            'vatNumber' => 'BE0123456789',
        ];
        
        $this->fakeClient->queueResponse($expectedCompany);

        // Act
        $result = $this->endpoint->get();

        // Assert
        $this->assertEquals($expectedCompany, $result);
        $this->fakeClient->assertRequestSent('/sapi/company', RequestMethod::GET);
    }

    #[Test]
    public function it_updates_company_information(): void
    {
        // Arrange
        $companyData = [
            'name' => 'Updated Company BVBA',
            'email' => 'new@company.com',
        ];
        
        $expectedResponse = array_merge($companyData, ['peppolId' => '0208:BE0123456789']);
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->update($companyData);

        // Assert
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals('Updated Company BVBA', $result['name']);
        $this->fakeClient->assertRequestSentWithData('/sapi/company', $companyData, RequestMethod::PUT);
    }
}
