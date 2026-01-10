<?php

namespace Tests\Unit\Filament\Actions\LetsPeppol\App\Company;

use App\Filament\Actions\LetsPeppol\App\Company\GetCompanyAction;
use App\Services\LetsPeppol\LetsPeppolClient;
use App\Services\LetsPeppol\Testing\FakeClient;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(GetCompanyAction::class)]
class GetCompanyActionTest extends TestCase
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
     * Test retrieving company information successfully
     * 
     * API Endpoint: GET /sapi/company
     * 
     * Expected Response:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "name": "Test Company BVBA",
     *   "vatNumber": "BE0123456789",
     *   "email": "info@testcompany.com",
     *   "address": "Main Street 123",
     *   "city": "Brussels",
     *   "postalCode": "1000",
     *   "country": "BE"
     * }
     */
    #[Test]
    public function it_retrieves_company_information_successfully(): void
    {
        // Arrange
        $expectedResponse = [
            'peppolId' => '0208:BE0123456789',
            'name' => 'Test Company BVBA',
            'vatNumber' => 'BE0123456789',
            'email' => 'info@testcompany.com',
            'address' => 'Main Street 123',
            'city' => 'Brussels',
            'postalCode' => '1000',
            'country' => 'BE',
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = GetCompanyAction::make();
        
        // Act
        $result = $action->call();
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/company');
        $this->fakeClient->assertRequestCount(1);
        
        // Verify the response structure and data integrity
        $this->assertIsArray($result);
        $this->assertArrayHasKey('peppolId', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('vatNumber', $result);
        $this->assertArrayHasKey('email', $result);
        
        // Verify actual values match expected format
        $this->assertEquals('0208:BE0123456789', $result['peppolId']);
        $this->assertStringStartsWith('BE', $result['vatNumber']);
        $this->assertMatchesRegularExpression('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $result['email']);
        $this->assertEquals('Test Company BVBA', $result['name']);
    }
    
    /**
     * Test handling missing company data
     * 
     * API Endpoint: GET /sapi/company
     * 
     * Scenario: Company not found or not registered
     */
    #[Test]
    public function it_handles_missing_company_data_gracefully(): void
    {
        // Arrange
        $this->fakeClient->queueException(
            new \App\Services\LetsPeppol\Exceptions\NotFoundException('Company not found')
        );
        
        $action = GetCompanyAction::make();
        
        // Act & Assert
        $this->expectException(\App\Services\LetsPeppol\Exceptions\NotFoundException::class);
        $this->expectExceptionMessage('Company not found');
        
        $action->call();
    }
}
