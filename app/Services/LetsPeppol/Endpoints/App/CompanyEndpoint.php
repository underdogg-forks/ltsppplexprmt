<?php

namespace App\Services\LetsPeppol\Endpoints\App;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Company endpoint client (AppService)
 * 
 * Namespace: App
 * Base URL: /sapi/company
 */
class CompanyEndpoint extends BaseEndpoint
{
    /**
     * Get company information
     * 
     * Response:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "name": "Company Name BVBA",
     *   "vatNumber": "BE0123456789",
     *   "email": "info@company.com",
     *   "phone": "+32 2 123 45 67",
     *   "street": "Main Street 1",
     *   "city": "Brussels",
     *   "postalCode": "1000",
     *   "country": "BE"
     * }
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
     * 
     * Request:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "name": "Company Name BVBA",
     *   "email": "newemail@company.com",
     *   "phone": "+32 2 123 45 67",
     *   "website": "https://company.com"
     * }
     * 
     * Response:
     * {
     *   "peppolId": "0208:BE0123456789",
     *   "name": "Company Name BVBA",
     *   "email": "newemail@company.com",
     *   ...
     * }
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
