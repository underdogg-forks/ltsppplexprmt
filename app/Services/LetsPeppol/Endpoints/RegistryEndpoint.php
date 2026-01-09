<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Registry endpoint client (for Proxy service)
 */
class RegistryEndpoint extends BaseEndpoint
{
    /**
     * Get registry information
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
     */
    public function delete(): void
    {
        $this->request(
            RequestMethod::DELETE,
            '/sapi/registry'
        );
    }
}
