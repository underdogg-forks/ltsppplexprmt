<?php

namespace App\Filament\Actions\LetsPeppol\Kyc\Password;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\TextInput;

/**
 * Filament action to change password
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\Kyc\PasswordEndpoint::change
 */
class ChangePasswordAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'change_password';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Change Password')
            ->icon('heroicon-o-key')
            ->form([
                TextInput::make('old_password')
                    ->label('Current Password')
                    ->password()
                    ->required()
                    ->revealable(),
                    
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
                        ->change($data['old_password'], $data['new_password']);

                    $this->notifySuccess(
                        'Password Changed',
                        'Your password has been changed successfully.'
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Change Password',
                        $e->getMessage()
                    );
                }
            });
    }
}
