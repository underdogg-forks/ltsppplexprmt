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
    
    #[Test]
    public function it_lists_documents_successfully(): void
    {
        // Arrange
        $expectedResponse = [
            'content' => [
                ['id' => 'doc-1', 'type' => 'INVOICE'],
                ['id' => 'doc-2', 'type' => 'CREDIT_NOTE'],
            ],
            'totalElements' => 50,
            'totalPages' => 3,
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = ListDocumentsAction::make();
        
        // Act
        $action->call([
            'type' => 'INVOICE',
            'direction' => null,
            'page' => 0,
            'size' => 20,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document');
        $this->fakeClient->assertRequestCount(1);
    }
    
    #[Test]
    public function it_lists_documents_with_filters(): void
    {
        // Arrange
        $expectedResponse = [
            'content' => [
                ['id' => 'doc-1', 'type' => 'INVOICE', 'direction' => 'OUTGOING'],
            ],
            'totalElements' => 10,
            'totalPages' => 1,
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = ListDocumentsAction::make();
        
        // Act
        $action->call([
            'type' => 'INVOICE',
            'direction' => 'OUTGOING',
            'page' => 0,
            'size' => 20,
        ]);
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/document');
        $this->fakeClient->assertRequestCount(1);
    }
}
