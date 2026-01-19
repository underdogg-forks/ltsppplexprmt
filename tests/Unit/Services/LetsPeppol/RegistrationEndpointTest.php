<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\Kyc\RegistrationEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(RegistrationEndpoint::class)]
class RegistrationEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private RegistrationEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new RegistrationEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_retrieves_company_information_by_peppol_id(): void
    {
        // Arrange
        $peppolId = '0208:BE0123456789';
        $expectedCompany = [
            'peppolId' => $peppolId,
            'name' => 'Company Name BVBA',
            'vatNumber' => 'BE0123456789',
        ];
        $this->fakeClient->queueResponse($expectedCompany);

        // Act
        $result = $this->endpoint->getCompany($peppolId);

        // Assert
        $this->assertEquals($expectedCompany, $result);
        $this->fakeClient->assertRequestSent("/api/register/company/{$peppolId}", RequestMethod::GET);
    }

    #[Test]
    public function it_confirms_company_and_sends_verification_email(): void
    {
        // Arrange
        $data = [
            'peppolId' => '0208:BE0123456789',
            'email' => 'admin@company.com',
            'name' => 'John Doe',
            'password' => 'securePassword123',
        ];
        $expectedResponse = [
            'status' => 'email_sent',
            'email' => 'admin@company.com',
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->confirmCompany($data);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent('/api/register/confirm-company', RequestMethod::POST);
        $this->fakeClient->assertRequestSentWithData($data);
    }

    #[Test]
    public function it_verifies_email_token(): void
    {
        // Arrange
        $token = 'verification-token-123';
        $expectedResponse = [
            'token' => 'session-token',
            'directors' => [
                ['id' => 1, 'name' => 'John Doe'],
            ],
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->verifyToken($token);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent('/api/register/verify', RequestMethod::POST);
    }

    #[Test]
    public function it_prepares_document_for_signing(): void
    {
        // Arrange
        $data = [
            'directorId' => 1,
            'token' => 'session-token',
        ];
        $expectedResponse = [
            'signingToken' => 'signing-token-123',
            'documentHash' => 'hash123',
            'status' => 'ready',
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->prepareSigning($data);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent('/api/identity/sign/prepare', RequestMethod::POST);
    }

    #[Test]
    public function it_retrieves_contract_pdf(): void
    {
        // Arrange
        $directorId = 1;
        $token = 'session-token';
        $pdfContent = 'PDF-binary-content';
        $this->fakeClient->queueResponse($pdfContent);

        // Act
        $result = $this->endpoint->getContract($directorId, $token);

        // Assert
        $this->assertEquals($pdfContent, $result);
        $this->fakeClient->assertRequestSent("/api/identity/contract/{$directorId}", RequestMethod::GET);
    }

    #[Test]
    public function it_finalizes_document_signing(): void
    {
        // Arrange
        $data = [
            'signature' => 'base64-signature',
            'signingToken' => 'signing-token-123',
        ];
        $pdfResponse = 'base64-signed-pdf';
        $this->fakeClient->queueResponse($pdfResponse);

        // Act
        $result = $this->endpoint->finalizeSigning($data);

        // Assert
        $this->assertArrayHasKey('pdf', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('provider', $result);
        $this->assertEquals('completed', $result['status']);
        $this->fakeClient->assertRequestSent('/api/identity/sign/finalize', RequestMethod::POST);
    }
}
