<?php

namespace Tests\Unit\Filament\Actions\LetsPeppol\App\Documents;

use App\Filament\Actions\LetsPeppol\App\Documents\CreateDocumentAction;
use App\Services\LetsPeppol\LetsPeppolClient;
use App\Services\LetsPeppol\Testing\FakeClient;
use App\Services\LetsPeppol\Endpoints\App\DocumentsEndpoint;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(CreateDocumentAction::class)]
class CreateDocumentActionTest extends TestCase
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
     * Test creating a production-ready document successfully
     * 
     * API Endpoint: POST /sapi/document
     * 
     * Request Payload:
     * - UBL XML content (valid Invoice/CreditNote)
     * - draft: false
     * - schedule: null (send immediately)
     * 
     * Expected Response:
     * {
     *   "id": "doc-123",
     *   "type": "INVOICE",
     *   "direction": "OUTGOING",
     *   "draft": false,
     *   "amount": 1250.50,
     *   "currency": "EUR",
     *   "issueDate": "2026-01-10",
     *   "status": "PENDING"
     * }
     */
    #[Test]
    public function it_creates_production_document_successfully(): void
    {
        // Arrange
        $ublXml = '<?xml version="1.0"?><Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"><ID>INV-2026-001</ID></Invoice>';
        $expectedResponse = [
            'id' => 'doc-123',
            'type' => 'INVOICE',
            'direction' => 'OUTGOING',
            'draft' => false,
            'amount' => 1250.50,
            'currency' => 'EUR',
            'issueDate' => '2026-01-10',
            'status' => 'PENDING',
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = CreateDocumentAction::make();
        
        // Act
        $result = $action->call([
            'ubl_xml' => $ublXml,
            'draft' => false,
            'schedule' => null,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document');
        $this->fakeClient->assertRequestCount(1);
        
        // Verify response structure for production document
        $this->assertIsArray($result);
        $this->assertFalse($result['draft'], 'Document should not be a draft');
        $this->assertEquals('PENDING', $result['status'], 'Production document should have PENDING status');
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('amount', $result);
        $this->assertArrayHasKey('currency', $result);
        $this->assertIsNumeric($result['amount']);
        $this->assertGreaterThan(0, $result['amount'], 'Amount should be positive');
    }
    
    /**
     * Test creating a draft document for review
     * 
     * API Endpoint: POST /sapi/document
     * 
     * Request Payload:
     * - UBL XML content
     * - draft: true
     * - schedule: null
     * 
     * Expected Response:
     * {
     *   "id": "doc-456",
     *   "type": "INVOICE",
     *   "draft": true,
     *   "status": "DRAFT"
     * }
     */
    #[Test]
    public function it_creates_draft_document_for_review(): void
    {
        // Arrange
        $ublXml = '<?xml version="1.0"?><Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"><ID>INV-DRAFT-001</ID></Invoice>';
        $expectedResponse = [
            'id' => 'doc-456',
            'type' => 'INVOICE',
            'draft' => true,
            'status' => 'DRAFT',
            'amount' => 500.00,
            'currency' => 'EUR',
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = CreateDocumentAction::make();
        
        // Act
        $result = $action->call([
            'ubl_xml' => $ublXml,
            'draft' => true,
            'schedule' => null,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document');
        $this->fakeClient->assertRequestCount(1);
        
        // Verify draft-specific properties
        $this->assertTrue($result['draft'], 'Document should be marked as draft');
        $this->assertEquals('DRAFT', $result['status'], 'Draft documents should have DRAFT status');
        $this->assertArrayHasKey('id', $result);
    }
    
    /**
     * Test handling invalid XML format error
     * 
     * API Endpoint: POST /sapi/document
     * 
     * Scenario: XML is malformed or doesn't conform to UBL 2.1 standard
     * Expected: ValidationException with clear error message
     */
    #[Test]
    public function it_handles_invalid_xml_format_error(): void
    {
        // Arrange
        $invalidXml = '<InvalidXml>Not UBL compliant</InvalidXml>';
        
        $this->fakeClient->queueException(
            new \App\Services\LetsPeppol\Exceptions\ValidationException('Invalid UBL XML format: Missing required Invoice namespace')
        );
        
        $action = CreateDocumentAction::make();
        
        // Act & Assert
        $this->expectException(\App\Services\LetsPeppol\Exceptions\ValidationException::class);
        $this->expectExceptionMessage('Invalid UBL XML format');
        
        $action->call([
            'ubl_xml' => $invalidXml,
            'draft' => false,
            'schedule' => null,
        ]);
    }
    
    /**
     * Test handling scheduled document creation
     * 
     * API Endpoint: POST /sapi/document
     * 
     * Request Payload:
     * - UBL XML content
     * - draft: false
     * - schedule: "2026-01-15T10:00:00Z"
     * 
     * Expected Response: Document created with SCHEDULED status
     */
    #[Test]
    public function it_creates_scheduled_document(): void
    {
        // Arrange
        $ublXml = '<?xml version="1.0"?><Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"><ID>INV-SCH-001</ID></Invoice>';
        $scheduleTime = '2026-01-15T10:00:00Z';
        $expectedResponse = [
            'id' => 'doc-789',
            'type' => 'INVOICE',
            'draft' => false,
            'status' => 'SCHEDULED',
            'scheduledAt' => $scheduleTime,
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = CreateDocumentAction::make();
        
        // Act
        $result = $action->call([
            'ubl_xml' => $ublXml,
            'draft' => false,
            'schedule' => $scheduleTime,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document');
        $this->assertEquals('SCHEDULED', $result['status']);
        $this->assertEquals($scheduleTime, $result['scheduledAt']);
    }
}
