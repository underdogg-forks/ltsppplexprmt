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
    
    #[Test]
    public function it_creates_document_successfully(): void
    {
        // Arrange
        $ublXml = '<Invoice>...</Invoice>';
        $expectedResponse = [
            'id' => 'doc-123',
            'type' => 'INVOICE',
            'draft' => false,
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = CreateDocumentAction::make();
        
        // Act
        $action->call([
            'ubl_xml' => $ublXml,
            'draft' => false,
            'schedule' => null,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document');
        $this->fakeClient->assertRequestCount(1);
    }
    
    #[Test]
    public function it_creates_draft_document(): void
    {
        // Arrange
        $ublXml = '<Invoice>...</Invoice>';
        $expectedResponse = [
            'id' => 'doc-456',
            'type' => 'INVOICE',
            'draft' => true,
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = CreateDocumentAction::make();
        
        // Act
        $action->call([
            'ubl_xml' => $ublXml,
            'draft' => true,
            'schedule' => null,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document');
        $this->fakeClient->assertRequestCount(1);
    }
    
    #[Test]
    public function it_handles_create_document_error(): void
    {
        // Arrange
        $ublXml = '<Invoice>...</Invoice>';
        
        $this->fakeClient->queueException(new \Exception('Invalid XML format'));
        
        $action = CreateDocumentAction::make();
        
        // Act & Assert - Exception should be caught and notification sent
        try {
            $action->call([
                'ubl_xml' => $ublXml,
                'draft' => false,
                'schedule' => null,
            ]);
        } catch (\Exception $e) {
            $this->assertEquals('Invalid XML format', $e->getMessage());
        }
    }
}
