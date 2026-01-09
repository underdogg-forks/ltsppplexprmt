<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Statistics endpoint client
 */
class StatisticsEndpoint extends BaseEndpoint
{
    /**
     * Get donation statistics
     */
    public function getDonation(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/api/stats/donation'
        );
    }

    /**
     * Get account totals
     */
    public function getAccount(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/stats/account'
        );
    }
}
