<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Endpoints\App\DocumentsEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DocumentsEndpointTest extends TestCase
{
    private ClientInterface $mockClient;
    private DocumentsEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockClient = $this->createMock(ClientInterface::class);
        $this->endpoint = new DocumentsEndpoint($this->mockClient);
    }

    #[Test]
    public function it_calls_client_with_correct_parameters_when_listing_documents(): void
    {
        // Arrange
        $filters = ['type' => 'INVOICE'];
        $expectedParams = array_merge($filters, [
            'page' => 0,
            'size' => 20,
        ]);

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::GET),
                $this->equalTo('/sapi/document'),
                $this->equalTo([]),
                $this->equalTo($expectedParams)
            )
            ->willReturn(['content' => []]);

        // Act
        $result = $this->endpoint->list($filters);

        // Assert
        $this->assertIsArray($result);
    }

    #[Test]
    public function it_retrieves_document_by_id(): void
    {
        // Arrange
        $documentId = 'test-doc-123';

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::GET),
                $this->equalTo("/sapi/document/{$documentId}")
            )
            ->willReturn(['id' => $documentId]);

        // Act
        $result = $this->endpoint->get($documentId);

        // Assert
        $this->assertEquals(['id' => $documentId], $result);
    }

    #[Test]
    public function it_creates_document_with_xml_content(): void
    {
        // Arrange
        $ublXml = '<Invoice>test</Invoice>';

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::POST),
                $this->equalTo('/sapi/document'),
                $this->equalTo(['body' => $ublXml]),
                $this->equalTo(['draft' => 'false']),
                $this->equalTo(['Content-Type' => 'text/xml'])
            )
            ->willReturn(['id' => 'new-doc-123']);

        // Act
        $result = $this->endpoint->create($ublXml);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('new-doc-123', $result['id']);
    }

    #[Test]
    public function it_deletes_document_by_id(): void
    {
        // Arrange
        $documentId = 'test-doc-123';

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::DELETE),
                $this->equalTo("/sapi/document/{$documentId}")
            )
            ->willReturn(null);

        // Act
        $this->endpoint->delete($documentId);

        // Assert - if no exception thrown, test passes
        $this->assertTrue(true);
    }

    #[Test]
    public function it_loops_through_all_documents_with_pagination(): void
    {
        // Arrange
        $allDocuments = [];
        
        $this->mockClient
            ->expects($this->exactly(3))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['content' => [['id' => '1'], ['id' => '2']], 'totalElements' => 5],
                ['content' => [['id' => '3'], ['id' => '4']], 'totalElements' => 5],
                ['content' => [['id' => '5']], 'totalElements' => 5]
            );

        // Act
        $this->endpoint->listAll(function($documents) use (&$allDocuments) {
            $allDocuments = array_merge($allDocuments, $documents);
        }, [], 2);

        // Assert
        $this->assertCount(5, $allDocuments);
        $this->assertEquals('1', $allDocuments[0]['id']);
        $this->assertEquals('5', $allDocuments[4]['id']);
    }
}

