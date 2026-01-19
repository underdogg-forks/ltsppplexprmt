<?php

namespace App\Filament\Actions\LetsPeppol\App\Partners;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\TextInput;

/**
 * Filament action to create a new partner
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\App\PartnersEndpoint::create
 */
class CreatePartnerAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'create_partner';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Create Partner')
            ->icon('heroicon-o-user-plus')
            ->form([
                TextInput::make('peppolId')
                    ->label('Peppol ID')
                    ->required()
                    ->hint('Format: 0208:BE0123456789'),
                    
                TextInput::make('name')
                    ->label('Partner Name')
                    ->required(),
                    
                TextInput::make('email')
                    ->label('Email')
                    ->email(),
                    
                TextInput::make('vatNumber')
                    ->label('VAT Number'),
                    
                TextInput::make('address')
                    ->label('Address'),
                    
                TextInput::make('country')
                    ->label('Country')
                    ->length(2)
                    ->hint('2-letter country code'),
            ])
            ->action(function (array $data): void {
                try {
                    $result = $this->getClient()
                        ->app()
                        ->partners()
                        ->create($data);

                    $partnerId = $result['id'] ?? 'unknown';
                    $this->notifySuccess(
                        'Partner Created',
                        "Partner {$data['name']} (ID: {$partnerId}) has been created successfully."
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Create Partner',
                        $e->getMessage()
                    );
                }
            });
    }
}
