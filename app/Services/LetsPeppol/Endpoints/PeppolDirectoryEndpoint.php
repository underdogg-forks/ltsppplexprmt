<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Peppol Directory endpoint client
 */
class PeppolDirectoryEndpoint extends BaseEndpoint
{
    /**
     * Search Peppol Directory
     */
    public function search(?string $name = null, ?string $participant = null): array
    {
        $params = array_filter([
            'name' => $name,
            'participant' => $participant,
        ]);

        return $this->request(
            RequestMethod::GET,
            '/api/peppol-directory',
            [],
            $params
        );
    }
}
