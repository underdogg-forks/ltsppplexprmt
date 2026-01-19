<?php

namespace App\Filament\Actions\LetsPeppol\App\Partners;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;

/**
 * Filament action to list all partners
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\App\PartnersEndpoint::list
 */
class ListPartnersAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'list_partners';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('List Partners')
            ->icon('heroicon-o-users')
            ->action(function (): void {
                try {
                    $result = $this->getClient()
                        ->app()
                        ->partners()
                        ->list();

                    $count = count($result);
                    
                    $this->notifySuccess(
                        'Partners Retrieved',
                        "Found {$count} partner(s)."
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to List Partners',
                        $e->getMessage()
                    );
                }
            });
    }
}
