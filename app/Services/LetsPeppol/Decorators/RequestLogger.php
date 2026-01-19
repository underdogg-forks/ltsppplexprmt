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

        // Filter out sensitive query parameters before logging
        $safeQueryParams = $this->filterSensitiveParams($queryParams);

        $this->logInfo('API Request', [
            'method' => $method->value,
            'endpoint' => $endpoint,
            'query_params' => $safeQueryParams,
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

    /**
     * Filter out sensitive query parameters from logs
     */
    private function filterSensitiveParams(array $params): array
    {
        $sensitiveKeys = ['token', 'authorization', 'password', 'secret', 'api_key', 'apikey'];
        
        return array_map(function ($value) use ($sensitiveKeys, $params) {
            $key = array_search($value, $params, true);
            if ($key !== false && in_array(strtolower($key), $sensitiveKeys)) {
                return '[REDACTED]';
            }
            return $value;
        }, $params);
    }
}
