<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Company endpoint client
 */
class CompanyEndpoint extends BaseEndpoint
{
    /**
     * Get company information
     */
    public function get(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/company'
        );
    }

    /**
     * Update company information
     */
    public function update(array $companyData): array
    {
        return $this->request(
            RequestMethod::PUT,
            '/sapi/company',
            $companyData
        );
    }
}
