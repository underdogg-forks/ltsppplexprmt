<?php

namespace App\Filament\Actions\LetsPeppol\Proxy\Registry;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;

/**
 * Filament action to get Peppol registry information
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\Proxy\RegistryEndpoint::get
 */
class GetRegistryAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'get_registry';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Get Registry Info')
            ->icon('heroicon-o-clipboard-document-list')
            ->action(function (): void {
                try {
                    $result = $this->getClient()
                        ->proxy()
                        ->registry()
                        ->get();

                    $peppolId = $result['peppolId'] ?? 'Unknown';
                    $companyName = $result['companyName'] ?? 'Unknown';
                    
                    $this->notifySuccess(
                        'Registry Information Retrieved',
                        "Company: {$companyName} (Peppol ID: {$peppolId})"
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Get Registry Info',
                        $e->getMessage()
                    );
                }
            });
    }
}
