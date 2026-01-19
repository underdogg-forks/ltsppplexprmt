<?php

namespace App\Filament\Actions\LetsPeppol\Kyc\Password;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

/**
 * Filament action to request password reset
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\Kyc\PasswordEndpoint::forgot
 */
class ForgotPasswordAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'forgot_password';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Forgot Password')
            ->icon('heroicon-o-envelope')
            ->form([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                    
                Select::make('language')
                    ->label('Language')
                    ->options([
                        'en' => 'English',
                        'nl' => 'Nederlands',
                        'fr' => 'Français',
                        'de' => 'Deutsch',
                    ])
                    ->default('en')
                    ->hint('Language for the password reset email'),
            ])
            ->action(function (array $data): void {
                try {
                    $this->getClient()
                        ->kyc()
                        ->password()
                        ->forgot($data['email'], $data['language'] ?? null);

                    $this->notifySuccess(
                        'Password Reset Email Sent',
                        'A password reset email has been sent to ' . $data['email']
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Send Reset Email',
                        $e->getMessage()
                    );
                }
            });
    }
}
