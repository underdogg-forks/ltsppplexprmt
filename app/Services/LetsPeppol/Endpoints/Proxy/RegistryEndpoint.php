<?php

namespace App\Services\LetsPeppol\Endpoints\Proxy;

use App\Services\LetsPeppol\Endpoints\BaseEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Registry endpoint client (ProxyService)
 * 
 * Namespace: Proxy
 * Base URL: /sapi/registry
 */
class RegistryEndpoint extends BaseEndpoint
{
    /**
     * Get registry information
     * 
     * Response:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "registered": true,
     *   "status": "ACTIVE",
     *   "registeredAt": "2024-01-01T00:00:00Z"
     * }
     */
    public function get(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/registry'
        );
    }

    /**
     * Register on Access Point
     * 
     * Request:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "documentTypes": ["urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"]
     * }
     * 
     * Response:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "registered": true,
     *   "status": "ACTIVE"
     * }
     */
    public function register(array $registrationData): array
    {
        return $this->request(
            RequestMethod::POST,
            '/sapi/registry',
            $registrationData
        );
    }

    /**
     * Unregister from Access Point
     * 
     * Response:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "registered": false,
     *   "status": "UNREGISTERED"
     * }
     */
    public function unregister(): array
    {
        return $this->request(
            RequestMethod::PUT,
            '/sapi/registry/unregister'
        );
    }

    /**
     * Remove from registry
     * 
     * Response: No content (204)
     */
    public function delete(): void
    {
        $this->request(
            RequestMethod::DELETE,
            '/sapi/registry'
        );
    }
}
