<?php

namespace App\Services\LetsPeppol\Endpoints\App;

use App\Services\LetsPeppol\Endpoints\BaseEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Partners endpoint client (AppService)
 * 
 * Namespace: App
 * Base URL: /sapi/partner
 */
class PartnersEndpoint extends BaseEndpoint
{
    /**
     * List partners
     * 
     * Response:
     * [
     *   {
     *     "id": 1,
     *     "peppolId": "0208:BE0987654321",
     *     "name": "Partner Company BVBA",
     *     "vatNumber": "BE0987654321",
     *     "email": "contact@partner.com",
     *     "street": "Partner Street 1",
     *     "city": "Brussels",
     *     "postalCode": "1000",
     *     "country": "BE"
     *   }
     * ]
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
     * 
     * Response:
     * [
     *   {
     *     "id": 1,
     *     "peppolId": "0208:BE0987654321",
     *     "name": "Partner Company BVBA"
     *   }
     * ]
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
     * 
     * Request:
     * {
     *   "peppolId": "0208:BE0987654321",
     *   "name": "Partner Company BVBA",
     *   "vatNumber": "BE0987654321",
     *   "email": "contact@partner.com",
     *   "street": "Partner Street 1",
     *   "city": "Brussels",
     *   "postalCode": "1000",
     *   "country": "BE"
     * }
     * 
     * Response:
     * {
     *   "id": 1,
     *   "peppolId": "0208:BE0987654321",
     *   "name": "Partner Company BVBA",
     *   ...
     * }
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
     * 
     * Request:
     * {
     *   "name": "Updated Partner Name",
     *   "email": "newemail@partner.com"
     * }
     * 
     * Response:
     * {
     *   "id": 1,
     *   "peppolId": "0208:BE0987654321",
     *   "name": "Updated Partner Name",
     *   ...
     * }
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
     * 
     * Response: No content (204)
     */
    public function delete(int $id): void
    {
        $this->request(
            RequestMethod::DELETE,
            "/sapi/partner/{$id}"
        );
    }
}
