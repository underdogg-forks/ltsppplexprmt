<?php

namespace Tests\Unit\Filament\Actions\LetsPeppol\App\Company;

use App\Filament\Actions\LetsPeppol\App\Company\GetCompanyAction;
use App\Services\LetsPeppol\LetsPeppolClient;
use App\Services\LetsPeppol\Testing\FakeClient;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(GetCompanyAction::class)]
class GetCompanyActionTest extends TestCase
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
    public function it_retrieves_company_information_successfully(): void
    {
        // Arrange
        $expectedResponse = [
            'peppolId' => '0208:BE0123456789',
            'name' => 'Test Company BVBA',
            'vatNumber' => 'BE0123456789',
            'email' => 'info@testcompany.com',
        ];
        
        $this->fakeClient->queueResponse($expectedResponse);
        
        $action = GetCompanyAction::make();
        
        // Act
        $action->call();
        
        // Assert
        $this->fakeClient->assertRequestSent('/sapi/company');
        $this->fakeClient->assertRequestCount(1);
    }
}
