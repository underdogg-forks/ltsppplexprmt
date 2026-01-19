<?php

namespace Tests\Unit\Filament\Actions\LetsPeppol\App\Documents;

use App\Filament\Actions\LetsPeppol\App\Documents\ListDocumentsAction;
use App\Services\LetsPeppol\LetsPeppolClient;
use App\Services\LetsPeppol\Testing\FakeClient;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ListDocumentsAction::class)]
class ListDocumentsActionTest extends TestCase
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
     * Test listing documents with pagination
     * 
     * API Endpoint: GET /sapi/document
     * 
     * Query Parameters:
     * - type: INVOICE (filter by document type)
     * - direction: null (all directions)
     * - page: 0 (first page)
     * - size: 20 (items per page)
     * 
     * Expected Response:
     * {
     *   "content": [
     *     {"id": "doc-1", "type": "INVOICE", "amount": 1000.00, "status": "SENT"},
     *     {"id": "doc-2", "type": "CREDIT_NOTE", "amount": 250.00, "status": "PENDING"}
     *   ],
     *   "totalElements": 50,
     *   "totalPages": 3,
     *   "number": 0,
     *   "size": 20
     * }
     */
    #[Test]
    public function it_lists_documents_with_pagination_successfully(): void
    {
        // Arrange
        $expectedResponse = [
            'content' => [
                [
                    'id' => 'doc-1',
                    'type' => 'INVOICE',
                    'amount' => 1000.00,
                    'currency' => 'EUR',
                    'status' => 'SENT',
                    'direction' => 'OUTGOING',
                ],
                [
                    'id' => 'doc-2',
                    'type' => 'CREDIT_NOTE',
                    'amount' => 250.00,
                    'currency' => 'EUR',
                    'status' => 'PENDING',
                    'direction' => 'OUTGOING',
                ],
            ],
            'totalElements' => 50,
            'totalPages' => 3,
            'number' => 0,
            'size' => 20,
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = ListDocumentsAction::make();
        
        // Act
        $result = $action->call([
            'type' => 'INVOICE',
            'direction' => null,
            'page' => 0,
            'size' => 20,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document');
        $this->fakeClient->assertRequestCount(1);
        
        // Verify pagination structure
        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertArrayHasKey('totalElements', $result);
        $this->assertArrayHasKey('totalPages', $result);
        
        // Verify document list is not empty and properly structured
        $this->assertIsArray($result['content']);
        $this->assertCount(2, $result['content']);
        $this->assertEquals(50, $result['totalElements']);
        $this->assertEquals(3, $result['totalPages']);
        
        // Verify individual document structure
        $firstDoc = $result['content'][0];
        $this->assertArrayHasKey('id', $firstDoc);
        $this->assertArrayHasKey('type', $firstDoc);
        $this->assertArrayHasKey('amount', $firstDoc);
        $this->assertArrayHasKey('status', $firstDoc);
        $this->assertIsNumeric($firstDoc['amount']);
    }
    
    /**
     * Test listing documents with direction and type filters
     * 
     * API Endpoint: GET /sapi/document
     * 
     * Query Parameters:
     * - type: INVOICE
     * - direction: OUTGOING (only outgoing documents)
     * - page: 0
     * - size: 20
     * 
     * Expected Response: Filtered list showing only outgoing invoices
     */
    #[Test]
    public function it_lists_outgoing_invoices_with_filters(): void
    {
        // Arrange
        $expectedResponse = [
            'content' => [
                [
                    'id' => 'doc-1',
                    'type' => 'INVOICE',
                    'direction' => 'OUTGOING',
                    'amount' => 1500.00,
                    'currency' => 'EUR',
                    'recipient' => '0208:BE0987654321',
                ],
            ],
            'totalElements' => 10,
            'totalPages' => 1,
            'number' => 0,
            'size' => 20,
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = ListDocumentsAction::make();
        
        // Act
        $result = $action->call([
            'type' => 'INVOICE',
            'direction' => 'OUTGOING',
            'page' => 0,
            'size' => 20,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document');
        $this->fakeClient->assertRequestCount(1);
        
        // Verify filter was applied correctly
        $this->assertEquals(10, $result['totalElements']);
        $this->assertCount(1, $result['content']);
        
        // Verify all results match the filter criteria
        foreach ($result['content'] as $doc) {
            $this->assertEquals('INVOICE', $doc['type']);
            $this->assertEquals('OUTGOING', $doc['direction']);
        }
    }
    
    /**
     * Test handling empty document list
     * 
     * API Endpoint: GET /sapi/document
     * 
     * Scenario: No documents match the filter criteria
     * Expected Response: Empty content array with zero totals
     */
    #[Test]
    public function it_handles_empty_document_list_gracefully(): void
    {
        // Arrange
        $expectedResponse = [
            'content' => [],
            'totalElements' => 0,
            'totalPages' => 0,
            'number' => 0,
            'size' => 20,
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = ListDocumentsAction::make();
        
        // Act
        $result = $action->call([
            'type' => 'CREDIT_NOTE',
            'direction' => 'INCOMING',
            'page' => 0,
            'size' => 20,
        ]);
        
        // Assert
        $this->assertIsArray($result['content']);
        $this->assertEmpty($result['content']);
        $this->assertEquals(0, $result['totalElements']);
        $this->assertEquals(0, $result['totalPages']);
    }
}
