<?php

namespace App\Providers;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Decorators\HttpExceptionHandler;
use App\Services\LetsPeppol\Decorators\RequestLogger;
use App\Services\LetsPeppol\HttpClient;
use Illuminate\Support\ServiceProvider;

class LetsPeppolServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the client interface with decorator chain
        $this->app->bind(ClientInterface::class, function ($app) {
            $baseUrl = config('services.letspeppol.app_url', 'https://app.letspeppol.org');
            
            return new RequestLogger(
                new HttpExceptionHandler(
                    new HttpClient($baseUrl)
                )
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
