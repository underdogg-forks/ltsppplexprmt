<?php

namespace App\Services\LetsPeppol\Decorators;

use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Traits\LogsActivity;

/**
 * Request logger decorator
 */
class RequestLogger extends ClientDecorator
{
    use LogsActivity;

    public function request(
        RequestMethod $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        array $headers = []
    ): mixed {
        $startTime = microtime(true);

        $this->logInfo('API Request', [
            'method' => $method->value,
            'endpoint' => $endpoint,
            'query_params' => $queryParams,
            'has_data' => !empty($data),
        ]);

        try {
            $response = parent::request($method, $endpoint, $data, $queryParams, $headers);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            $this->logInfo('API Response', [
                'method' => $method->value,
                'endpoint' => $endpoint,
                'duration_ms' => $duration,
                'success' => true,
            ]);

            return $response;
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            $this->logError('API Request Failed', [
                'method' => $method->value,
                'endpoint' => $endpoint,
                'duration_ms' => $duration,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
