<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\App\DocumentsEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DocumentsEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private DocumentsEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new DocumentsEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_calls_client_with_correct_parameters_when_listing_documents(): void
    {
        // Arrange
        $filters = ['type' => 'INVOICE'];
        $this->fakeClient->queueResponse(['content' => []]);

        // Act
        $result = $this->endpoint->list($filters);

        // Assert
        $this->assertIsArray($result);
        $this->fakeClient->assertRequestSent('/sapi/document', RequestMethod::GET);
    }

    #[Test]
    public function it_retrieves_document_by_id(): void
    {
        // Arrange
        $documentId = 'test-doc-123';
        $this->fakeClient->queueResponse(['id' => $documentId]);

        // Act
        $result = $this->endpoint->get($documentId);

        // Assert
        $this->assertEquals(['id' => $documentId], $result);
        $this->fakeClient->assertRequestSent("/sapi/document/{$documentId}", RequestMethod::GET);
    }

    #[Test]
    public function it_creates_document_with_xml_content(): void
    {
        // Arrange
        $ublXml = '<Invoice>test</Invoice>';
        $this->fakeClient->queueResponse(['id' => 'new-doc-123']);

        // Act
        $result = $this->endpoint->create($ublXml);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('new-doc-123', $result['id']);
        $this->fakeClient->assertRequestSent('/sapi/document', RequestMethod::POST);
    }

    #[Test]
    public function it_deletes_document_by_id(): void
    {
        // Arrange
        $documentId = 'test-doc-123';
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->delete($documentId);

        // Assert
        $this->fakeClient->assertRequestSent("/sapi/document/{$documentId}", RequestMethod::DELETE);
    }

    #[Test]
    public function it_loops_through_all_documents_with_pagination(): void
    {
        // Arrange
        $allDocuments = [];
        
        $this->fakeClient->queueResponses([
            ['content' => [['id' => '1'], ['id' => '2']], 'totalElements' => 5],
            ['content' => [['id' => '3'], ['id' => '4']], 'totalElements' => 5],
            ['content' => [['id' => '5']], 'totalElements' => 5]
        ]);

        // Act
        $this->endpoint->listAll(function($documents) use (&$allDocuments) {
            $allDocuments = array_merge($allDocuments, $documents);
        }, [], 2);

        // Assert
        $this->assertCount(5, $allDocuments);
        $this->assertEquals('1', $allDocuments[0]['id']);
        $this->assertEquals('5', $allDocuments[4]['id']);
        $this->fakeClient->assertRequestCount(3);
    }

    #[Test]
    public function it_validates_ubl_xml(): void
    {
        // Arrange
        $ublXml = '<Invoice>test</Invoice>';
        $this->fakeClient->queueResponse(['valid' => true, 'errors' => []]);

        // Act
        $result = $this->endpoint->validate($ublXml);

        // Assert
        $this->assertTrue($result['valid']);
        $this->fakeClient->assertRequestSent('/sapi/document/validate', RequestMethod::POST);
    }

    #[Test]
    public function it_marks_document_as_read(): void
    {
        // Arrange
        $documentId = 'test-doc-123';
        $this->fakeClient->queueResponse(['id' => $documentId, 'read' => true]);

        // Act
        $result = $this->endpoint->markRead($documentId);

        // Assert
        $this->assertTrue($result['read']);
        $this->fakeClient->assertRequestSent("/sapi/document/{$documentId}/read", RequestMethod::PUT);
    }

    #[Test]
    public function it_marks_document_as_paid(): void
    {
        // Arrange
        $documentId = 'test-doc-123';
        $this->fakeClient->queueResponse(['id' => $documentId, 'paid' => true]);

        // Act
        $result = $this->endpoint->markPaid($documentId);

        // Assert
        $this->assertTrue($result['paid']);
        $this->fakeClient->assertRequestSent("/sapi/document/{$documentId}/paid", RequestMethod::PUT);
    }
}

