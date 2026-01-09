<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Endpoints\DocumentsEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
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

    public function test_list_calls_client_with_correct_parameters(): void
    {
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

        $result = $this->endpoint->list($filters);

        $this->assertIsArray($result);
    }

    public function test_get_calls_client_with_document_id(): void
    {
        $documentId = 'test-doc-123';

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::GET),
                $this->equalTo("/sapi/document/{$documentId}")
            )
            ->willReturn(['id' => $documentId]);

        $result = $this->endpoint->get($documentId);

        $this->assertEquals(['id' => $documentId], $result);
    }

    public function test_create_calls_client_with_xml_content(): void
    {
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

        $result = $this->endpoint->create($ublXml);

        $this->assertIsArray($result);
        $this->assertEquals('new-doc-123', $result['id']);
    }

    public function test_delete_calls_client_with_document_id(): void
    {
        $documentId = 'test-doc-123';

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo(RequestMethod::DELETE),
                $this->equalTo("/sapi/document/{$documentId}")
            )
            ->willReturn(null);

        $this->endpoint->delete($documentId);
    }
}
