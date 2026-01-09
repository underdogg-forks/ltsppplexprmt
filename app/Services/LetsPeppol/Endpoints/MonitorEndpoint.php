<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Monitor endpoint client
 */
class MonitorEndpoint extends BaseEndpoint
{
    /**
     * Health check
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
     */
    public function topUpBalance(int $amount): string
    {
        return $this->request(
            RequestMethod::GET,
            "/api/monitor/{$amount}"
        );
    }
}
