<?php

namespace App\Filament\Actions\LetsPeppol\Kyc\Authentication;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\TextInput;

/**
 * Filament action to authenticate with LetsPeppol
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\Kyc\AuthenticationEndpoint::authenticate
 */
class AuthenticateAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'authenticate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Authenticate')
            ->icon('heroicon-o-key')
            ->form([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                    
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->revealable(),
            ])
            ->action(function (array $data): void {
                try {
                    $token = $this->getClient()
                        ->kyc()
                        ->authentication()
                        ->authenticate($data['email'], $data['password']);

                    // Store token in client
                    $this->getClient()->setToken($token);
                    
                    $this->notifySuccess(
                        'Authentication Successful',
                        'You have been authenticated successfully.'
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Authentication Failed',
                        $e->getMessage()
                    );
                }
            });
    }
}
