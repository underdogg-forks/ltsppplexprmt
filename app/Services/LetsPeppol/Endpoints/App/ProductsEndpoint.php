<?php

namespace App\Services\LetsPeppol\Endpoints\App;

use App\Services\LetsPeppol\Endpoints\BaseEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Products endpoint client (AppService)
 * 
 * Namespace: App
 * Base URL: /sapi/product
 */
class ProductsEndpoint extends BaseEndpoint
{
    /**
     * List products
     * 
     * Response:
     * [
     *   {
     *     "id": 1,
     *     "name": "Laptop",
     *     "description": "High-performance laptop",
     *     "price": 999.99,
     *     "unit": "piece",
     *     "sku": "LAPTOP-001",
     *     "categoryId": 1
     *   }
     * ]
     */
    public function list(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/product'
        );
    }

    /**
     * Create product
     * 
     * Request:
     * {
     *   "name": "Laptop",
     *   "description": "High-performance laptop",
     *   "price": 999.99,
     *   "unit": "piece",
     *   "sku": "LAPTOP-001",
     *   "categoryId": 1
     * }
     * 
     * Response:
     * {
     *   "id": 1,
     *   "name": "Laptop",
     *   ...
     * }
     */
    public function create(array $productData): array
    {
        return $this->request(
            RequestMethod::POST,
            '/sapi/product',
            $productData
        );
    }

    /**
     * Update product
     * 
     * Request:
     * {
     *   "name": "Updated Laptop",
     *   "price": 899.99
     * }
     * 
     * Response:
     * {
     *   "id": 1,
     *   "name": "Updated Laptop",
     *   "price": 899.99,
     *   ...
     * }
     */
    public function update(int $id, array $productData): array
    {
        return $this->request(
            RequestMethod::PUT,
            "/sapi/product/{$id}",
            $productData
        );
    }

    /**
     * Delete product
     * 
     * Response: No content (204)
     */
    public function delete(int $id): void
    {
        $this->request(
            RequestMethod::DELETE,
            "/sapi/product/{$id}"
        );
    }
}
