<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\Proxy\DocumentsEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DocumentsEndpoint::class)]
class ProxyDocumentsEndpointTest extends TestCase
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
    public function it_retrieves_all_new_documents(): void
    {
        // Arrange
        $expectedDocuments = [
            [
                'id' => 'uuid-1',
                'documentType' => 'INVOICE',
                'direction' => 'INCOMING',
            ],
        ];
        $this->fakeClient->queueResponse($expectedDocuments);

        // Act
        $result = $this->endpoint->getAllNew();

        // Assert
        $this->assertEquals($expectedDocuments, $result);
        $this->fakeClient->assertRequestSent('/sapi/document', RequestMethod::GET);
    }

    #[Test]
    public function it_retrieves_all_new_documents_with_custom_size(): void
    {
        // Arrange
        $size = 50;
        $this->fakeClient->queueResponse([]);

        // Act
        $this->endpoint->getAllNew($size);

        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document', RequestMethod::GET);
    }

    #[Test]
    public function it_retrieves_status_updates_for_documents(): void
    {
        // Arrange
        $documentIds = ['uuid-1', 'uuid-2'];
        $expectedUpdates = [
            ['id' => 'uuid-1', 'status' => 'DELIVERED'],
            ['id' => 'uuid-2', 'status' => 'FAILED'],
        ];
        $this->fakeClient->queueResponse($expectedUpdates);

        // Act
        $result = $this->endpoint->getStatusUpdates($documentIds);

        // Assert
        $this->assertEquals($expectedUpdates, $result);
        $this->fakeClient->assertRequestSent('/sapi/document/status', RequestMethod::POST);
        $this->fakeClient->assertRequestSentWithData($documentIds);
    }

    #[Test]
    public function it_retrieves_document_by_id(): void
    {
        // Arrange
        $documentId = 'uuid-123';
        $expectedDocument = [
            'id' => $documentId,
            'documentType' => 'INVOICE',
            'ublXml' => '<Invoice>...</Invoice>',
        ];
        $this->fakeClient->queueResponse($expectedDocument);

        // Act
        $result = $this->endpoint->get($documentId);

        // Assert
        $this->assertEquals($expectedDocument, $result);
        $this->fakeClient->assertRequestSent("/sapi/document/{$documentId}", RequestMethod::GET);
    }

    #[Test]
    public function it_creates_document_to_send(): void
    {
        // Arrange
        $documentData = [
            'recipientId' => '0208:BE0987654321',
            'documentType' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            'ublXml' => '<Invoice>...</Invoice>',
        ];
        $expectedResponse = [
            'id' => 'uuid-123',
            'status' => 'QUEUED',
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->create($documentData);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent('/sapi/document', RequestMethod::POST);
    }

    #[Test]
    public function it_updates_document(): void
    {
        // Arrange
        $documentId = 'uuid-123';
        $documentData = ['ublXml' => '<Invoice>...</Invoice>'];
        $expectedResponse = [
            'id' => $documentId,
            'status' => 'UPDATED',
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->update($documentId, $documentData);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent("/sapi/document/{$documentId}", RequestMethod::PUT);
    }

    #[Test]
    public function it_reschedules_document_sending(): void
    {
        // Arrange
        $documentId = 'uuid-123';
        $documentData = ['scheduledAt' => '2024-01-02T00:00:00Z'];
        $expectedResponse = [
            'id' => $documentId,
            'status' => 'RESCHEDULED',
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->reschedule($documentId, $documentData);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent("/sapi/document/{$documentId}/send", RequestMethod::PUT);
    }

    #[Test]
    public function it_marks_document_as_downloaded(): void
    {
        // Arrange
        $documentId = 'uuid-123';
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->markDownloaded($documentId);

        // Assert
        $this->fakeClient->assertRequestSent("/sapi/document/{$documentId}/downloaded", RequestMethod::PUT);
    }

    #[Test]
    public function it_marks_multiple_documents_as_downloaded(): void
    {
        // Arrange
        $documentIds = ['uuid-1', 'uuid-2', 'uuid-3'];
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->markDownloadedBatch($documentIds);

        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document/downloaded', RequestMethod::PUT);
        $this->fakeClient->assertRequestSentWithData($documentIds);
    }

    #[Test]
    public function it_deletes_document(): void
    {
        // Arrange
        $documentId = 'uuid-123';
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->delete($documentId);

        // Assert
        $this->fakeClient->assertRequestSent("/sapi/document/{$documentId}", RequestMethod::DELETE);
    }
}
