<?php

namespace App\Filament\Actions\LetsPeppol;

use App\Services\LetsPeppol\LetsPeppolClient;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * Base class for all LetsPeppol API actions
 * 
 * Provides common functionality for interacting with the LetsPeppol API
 * through Filament actions.
 */
abstract class BaseLetsPeppolAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->modalWidth('2xl');
    }
    
    /**
     * Get the LetsPeppol client instance
     */
    protected function getClient(): LetsPeppolClient
    {
        return app(LetsPeppolClient::class);
    }
    
    /**
     * Send success notification
     */
    protected function notifySuccess(string $title, ?string $body = null): void
    {
        Notification::make()
            ->success()
            ->title($title)
            ->body($body)
            ->send();
    }
    
    /**
     * Send error notification
     */
    protected function notifyError(string $title, ?string $body = null): void
    {
        Notification::make()
            ->danger()
            ->title($title)
            ->body($body)
            ->send();
    }
    
    /**
     * Send info notification
     */
    protected function notifyInfo(string $title, ?string $body = null): void
    {
        Notification::make()
            ->info()
            ->title($title)
            ->body($body)
            ->send();
    }
}
