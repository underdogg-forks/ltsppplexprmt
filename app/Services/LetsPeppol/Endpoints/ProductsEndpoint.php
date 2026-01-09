<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Products endpoint client
 */
class ProductsEndpoint extends BaseEndpoint
{
    /**
     * List products
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
     */
    public function delete(int $id): void
    {
        $this->request(
            RequestMethod::DELETE,
            "/sapi/product/{$id}"
        );
    }
}
