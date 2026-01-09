<?php

namespace App\Services\LetsPeppol\Decorators;

use App\Services\LetsPeppol\Enums\RequestMethod;
use Illuminate\Support\Facades\Log;

/**
 * Request logger decorator
 */
class RequestLogger extends ClientDecorator
{
    public function request(
        RequestMethod $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        array $headers = []
    ): mixed {
        $startTime = microtime(true);

        Log::info('API Request', [
            'method' => $method->value,
            'endpoint' => $endpoint,
            'query_params' => $queryParams,
            'has_data' => !empty($data),
        ]);

        try {
            $response = parent::request($method, $endpoint, $data, $queryParams, $headers);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::info('API Response', [
                'method' => $method->value,
                'endpoint' => $endpoint,
                'duration_ms' => $duration,
                'success' => true,
            ]);

            return $response;
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('API Request Failed', [
                'method' => $method->value,
                'endpoint' => $endpoint,
                'duration_ms' => $duration,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
