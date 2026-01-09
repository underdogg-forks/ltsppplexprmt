<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Endpoints\App\CompanyEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CompanyEndpointTest extends TestCase
{
    private ClientInterface $mockClient;
    private CompanyEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockClient = $this->createMock(ClientInterface::class);
        $this->endpoint = new CompanyEndpoint($this->mockClient);
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

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::GET),
                $this->equalTo('/sapi/company')
            )
            ->willReturn($expectedCompany);

        // Act
        $result = $this->endpoint->get();

        // Assert
        $this->assertEquals($expectedCompany, $result);
    }

    #[Test]
    public function it_updates_company_information(): void
    {
        // Arrange
        $companyData = [
            'name' => 'Updated Company BVBA',
            'email' => 'new@company.com',
        ];

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::PUT),
                $this->equalTo('/sapi/company'),
                $this->equalTo($companyData)
            )
            ->willReturn(array_merge($companyData, ['peppolId' => '0208:BE0123456789']));

        // Act
        $result = $this->endpoint->update($companyData);

        // Assert
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals('Updated Company BVBA', $result['name']);
    }
}
