<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\Kyc\AuthenticationEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AuthenticationEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private AuthenticationEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new AuthenticationEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_authenticates_with_email_and_password(): void
    {
        // Arrange
        $email = 'user@example.com';
        $password = 'secure-password';
        $expectedToken = 'jwt-token-12345';
        
        $this->fakeClient->queueResponse($expectedToken);

        // Act
        $result = $this->endpoint->authenticate($email, $password);

        // Assert
        $this->assertEquals($expectedToken, $result);
        $this->fakeClient->assertRequestSent('/sapi/login', RequestMethod::POST);
    }

    #[Test]
    public function it_retrieves_account_information(): void
    {
        // Arrange
        $expectedAccount = [
            'email' => 'user@example.com',
            'company' => 'Test Company BVBA',
            'peppolId' => '0208:BE0123456789',
        ];
        
        $this->fakeClient->queueResponse($expectedAccount);

        // Act
        $result = $this->endpoint->getAccountInfo();

        // Assert
        $this->assertEquals('user@example.com', $result['email']);
        $this->assertEquals('Test Company BVBA', $result['company']);
        $this->fakeClient->assertRequestSent('/sapi/info', RequestMethod::GET);
    }

    #[Test]
    public function it_searches_companies_by_query(): void
    {
        // Arrange
        $query = 'Test Company';
        $expectedResults = [
            ['name' => 'Test Company BVBA', 'vatNumber' => 'BE0123456789'],
            ['name' => 'Test Company NV', 'vatNumber' => 'BE0987654321'],
        ];
        
        $this->fakeClient->queueResponse($expectedResults);

        // Act
        $result = $this->endpoint->searchCompanies($query);

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals('Test Company BVBA', $result[0]['name']);
        $this->fakeClient->assertRequestSent('/sapi/company/search', RequestMethod::GET);
    }

    #[Test]
    public function it_registers_on_peppol_directory(): void
    {
        // Arrange
        $expectedResponse = [
            'token' => 'new-jwt-token',
            'status' => 'updated'
        ];
        
        $this->fakeClient->queueResponse('new-jwt-token');

        // Act
        $result = $this->endpoint->registerPeppol();

        // Assert
        $this->assertEquals('updated', $result['status']);
        $this->assertArrayHasKey('token', $result);
        $this->fakeClient->assertRequestSent('/sapi/company/peppol/register', RequestMethod::POST);
    }

    #[Test]
    public function it_handles_already_registered_status(): void
    {
        // Arrange
        $this->fakeClient->queueResponse(['status' => 'already_registered']);

        // Act
        $result = $this->endpoint->registerPeppol();

        // Assert
        $this->assertEquals('already_registered', $result['status']);
        $this->fakeClient->assertRequestSent('/sapi/company/peppol/register', RequestMethod::POST);
    }

    #[Test]
    public function it_unregisters_from_peppol_directory(): void
    {
        // Arrange
        $expectedResponse = [
            'token' => 'new-jwt-token',
            'status' => 'unregistered'
        ];
        
        $this->fakeClient->queueResponse('new-jwt-token');

        // Act
        $result = $this->endpoint->unregisterPeppol();

        // Assert
        $this->assertEquals('unregistered', $result['status']);
        $this->assertArrayHasKey('token', $result);
        $this->fakeClient->assertRequestSent('/sapi/company/peppol/unregister', RequestMethod::POST);
    }
}
