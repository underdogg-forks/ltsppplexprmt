<?php

namespace App\Services\LetsPeppol\Endpoints\App;

use App\Services\LetsPeppol\Endpoints\BaseEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Statistics endpoint client (AppService)
 * 
 * Namespace: App
 * Base URL: /sapi/statistics
 */
class StatisticsEndpoint extends BaseEndpoint
{
    /**
     * Get donation statistics
     * 
     * Response:
     * {
     *   "totalDonations": 100.00,
     *   "currency": "EUR",
     *   "donationCount": 10,
     *   "lastDonation": "2024-01-01T00:00:00Z"
     * }
     */
    public function getDonation(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/statistics/donation'
        );
    }

    /**
     * Get account totals/statistics
     * 
     * Response:
     * {
     *   "totalInvoices": 150,
     *   "totalAmount": 50000.00,
     *   "totalPaid": 45000.00,
     *   "totalUnpaid": 5000.00,
     *   "currency": "EUR",
     *   "period": {
     *     "from": "2024-01-01T00:00:00Z",
     *     "to": "2024-12-31T23:59:59Z"
     *   }
     * }
     */
    public function getAccount(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/statistics/account'
        );
    }
}
