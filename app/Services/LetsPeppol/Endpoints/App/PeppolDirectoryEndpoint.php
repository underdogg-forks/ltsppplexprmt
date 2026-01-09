<?php

namespace App\Services\LetsPeppol\Endpoints\App;

use App\Services\LetsPeppol\Endpoints\BaseEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Peppol Directory endpoint client (AppService)
 * 
 * Namespace: App
 * Base URL: /sapi/peppol-directory
 */
class PeppolDirectoryEndpoint extends BaseEndpoint
{
    /**
     * Search Peppol Directory
     * 
     * Response:
     * [
     *   {
     *     "peppolId": "0208:BE0123456789",
     *     "name": "Company Name BVBA",
     *     "country": "BE",
     *     "registered": true,
     *     "documentTypes": [
     *       "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
     *     ]
     *   }
     * ]
     */
    public function search(?string $name = null, ?string $participant = null): array
    {
        $params = array_filter([
            'name' => $name,
            'participant' => $participant,
        ]);

        return $this->request(
            RequestMethod::GET,
            '/sapi/peppol-directory/search',
            [],
            $params
        );
    }
}
