<?php

namespace App\Filament\Actions\LetsPeppol\Kyc\Authentication;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;

/**
 * Filament action to get account information
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\Kyc\AuthenticationEndpoint::getAccountInfo
 */
class GetAccountInfoAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'get_account_info';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Get Account Info')
            ->icon('heroicon-o-user-circle')
            ->action(function (): void {
                try {
                    $result = $this->getClient()
                        ->kyc()
                        ->authentication()
                        ->getAccountInfo();

                    $email = $result['email'] ?? 'Unknown';
                    $name = $result['name'] ?? 'Unknown';
                    
                    $this->notifySuccess(
                        'Account Information Retrieved',
                        "User: {$name} ({$email})"
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Get Account Info',
                        $e->getMessage()
                    );
                }
            });
    }
}
