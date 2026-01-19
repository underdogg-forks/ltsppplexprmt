<?php

namespace App\Filament\Actions\LetsPeppol\Kyc\Authentication;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\TextInput;

/**
 * Filament action to search companies
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\Kyc\AuthenticationEndpoint::searchCompanies
 */
class SearchCompaniesAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'search_companies';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Search Companies')
            ->icon('heroicon-o-magnifying-glass')
            ->form([
                TextInput::make('vatNumber')
                    ->label('VAT Number')
                    ->hint('Optional: Search by VAT number'),
                    
                TextInput::make('peppolId')
                    ->label('Peppol ID')
                    ->hint('Optional: Search by Peppol ID'),
                    
                TextInput::make('companyName')
                    ->label('Company Name')
                    ->hint('Optional: Search by company name'),
            ])
            ->action(function (array $data): void {
                try {
                    $result = $this->getClient()
                        ->kyc()
                        ->authentication()
                        ->searchCompanies(
                            $data['vatNumber'] ?? null,
                            $data['peppolId'] ?? null,
                            $data['companyName'] ?? null
                        );

                    $count = count($result);
                    
                    $this->notifySuccess(
                        'Companies Found',
                        "Found {$count} company/companies matching the search criteria."
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Search Failed',
                        $e->getMessage()
                    );
                }
            });
    }
}
