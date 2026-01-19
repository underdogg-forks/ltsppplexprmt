<?php

namespace App\Services\LetsPeppol\Endpoints\Proxy;

use App\Services\LetsPeppol\Endpoints\BaseEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Monitor endpoint client (ProxyService)
 * 
 * Namespace: Proxy
 * Base URL: /api/monitor
 */
class MonitorEndpoint extends BaseEndpoint
{
    /**
     * Health check
     * 
     * Response:
     * "OK"
     */
    public function healthCheck(): string
    {
        return $this->request(
            RequestMethod::GET,
            '/api/monitor'
        );
    }

    /**
     * Top up balance (for testing/monitoring)
     * 
     * Response:
     * "Balance topped up with {amount}"
     */
    public function topUpBalance(int $amount): string
    {
        return $this->request(
            RequestMethod::GET,
            "/api/monitor/{$amount}"
        );
    }
}
