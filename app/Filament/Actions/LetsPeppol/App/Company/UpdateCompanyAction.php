<?php

namespace App\Filament\Actions\LetsPeppol\App\Company;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\TextInput;

/**
 * Filament action to update company information
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\App\CompanyEndpoint::update
 */
class UpdateCompanyAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'update_company';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Update Company')
            ->icon('heroicon-o-pencil-square')
            ->form([
                TextInput::make('name')
                    ->label('Company Name')
                    ->required(),
                    
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                    
                TextInput::make('address')
                    ->label('Address'),
                    
                TextInput::make('city')
                    ->label('City'),
                    
                TextInput::make('postal_code')
                    ->label('Postal Code'),
                    
                TextInput::make('country')
                    ->label('Country')
                    ->length(2)
                    ->hint('2-letter country code (e.g., BE, NL)'),
            ])
            ->action(function (array $data): void {
                try {
                    $result = $this->getClient()
                        ->app()
                        ->company()
                        ->update($data);

                    $this->notifySuccess(
                        'Company Updated',
                        'Company information has been updated successfully.'
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Update Company',
                        $e->getMessage()
                    );
                }
            });
    }
}
