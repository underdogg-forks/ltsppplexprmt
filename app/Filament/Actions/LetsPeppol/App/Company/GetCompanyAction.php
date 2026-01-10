<?php

namespace App\Filament\Actions\LetsPeppol\App\Company;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;

/**
 * Filament action to get company information
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\App\CompanyEndpoint::get
 */
class GetCompanyAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'get_company';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Get Company Info')
            ->icon('heroicon-o-building-office')
            ->action(function (): void {
                try {
                    $result = $this->getClient()
                        ->app()
                        ->company()
                        ->get();

                    $companyName = $result['name'] ?? 'Unknown';
                    $peppolId = $result['peppolId'] ?? 'N/A';
                    
                    $this->notifySuccess(
                        'Company Information Retrieved',
                        "Company: {$companyName} (Peppol ID: {$peppolId})"
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Get Company Info',
                        $e->getMessage()
                    );
                }
            });
    }
}
