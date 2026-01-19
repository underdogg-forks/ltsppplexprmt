<?php

namespace App\Filament\Actions\LetsPeppol\Proxy\Monitor;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;

/**
 * Filament action to perform health check
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\Proxy\MonitorEndpoint::healthCheck
 */
class HealthCheckAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'health_check';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Health Check')
            ->icon('heroicon-o-heart')
            ->action(function (): void {
                try {
                    $result = $this->getClient()
                        ->proxy()
                        ->monitor()
                        ->healthCheck();

                    if ($result === 'OK') {
                        $this->notifySuccess(
                            'Health Check Passed',
                            'The LetsPeppol API is healthy and responding.'
                        );
                    } else {
                        $this->notifyInfo(
                            'Health Check Result',
                            'Response: ' . $result
                        );
                    }
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Health Check Failed',
                        $e->getMessage()
                    );
                }
            });
    }
}
