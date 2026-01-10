<?php

namespace App\Filament\Actions\LetsPeppol\Kyc\Password;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\TextInput;

/**
 * Filament action to reset password with token
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\Kyc\PasswordEndpoint::reset
 */
class ResetPasswordAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'reset_password';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Reset Password')
            ->icon('heroicon-o-lock-closed')
            ->form([
                TextInput::make('token')
                    ->label('Reset Token')
                    ->required()
                    ->hint('Token received via email'),
                    
                TextInput::make('new_password')
                    ->label('New Password')
                    ->password()
                    ->required()
                    ->revealable()
                    ->minLength(8),
                    
                TextInput::make('new_password_confirmation')
                    ->label('Confirm New Password')
                    ->password()
                    ->required()
                    ->revealable()
                    ->same('new_password'),
            ])
            ->action(function (array $data): void {
                try {
                    $this->getClient()
                        ->kyc()
                        ->password()
                        ->reset($data['token'], $data['new_password']);

                    $this->notifySuccess(
                        'Password Reset Successful',
                        'Your password has been reset successfully.'
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Reset Password',
                        $e->getMessage()
                    );
                }
            });
    }
}
