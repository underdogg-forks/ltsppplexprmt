<?php

namespace App\Services\LetsPeppol\Endpoints\App;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Partners endpoint client
 */
class PartnersEndpoint extends ..\BaseEndpoint
{
    /**
     * List partners
     */
    public function list(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/partner'
        );
    }

    /**
     * Search partners by Peppol ID
     */
    public function search(string $peppolId): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/partner/search',
            [],
            ['peppolId' => $peppolId]
        );
    }

    /**
     * Create partner
     */
    public function create(array $partnerData): array
    {
        return $this->request(
            RequestMethod::POST,
            '/sapi/partner',
            $partnerData
        );
    }

    /**
     * Update partner
     */
    public function update(int $id, array $partnerData): array
    {
        return $this->request(
            RequestMethod::PUT,
            "/sapi/partner/{$id}",
            $partnerData
        );
    }

    /**
     * Delete partner
     */
    public function delete(int $id): void
    {
        $this->request(
            RequestMethod::DELETE,
            "/sapi/partner/{$id}"
        );
    }
}
