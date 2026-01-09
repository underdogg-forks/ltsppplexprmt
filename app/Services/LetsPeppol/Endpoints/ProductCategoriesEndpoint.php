<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Product Categories endpoint client
 */
class ProductCategoriesEndpoint extends BaseEndpoint
{
    /**
     * List root categories
     */
    public function listRoot(bool $deep = false): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/product-category',
            [],
            ['deep' => $deep ? 'true' : 'false']
        );
    }

    /**
     * List all categories flat
     */
    public function listAll(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/product-category/all'
        );
    }

    /**
     * Get category by ID
     */
    public function get(int $id, bool $deep = false): array
    {
        return $this->request(
            RequestMethod::GET,
            "/sapi/product-category/{$id}",
            [],
            ['deep' => $deep ? 'true' : 'false']
        );
    }

    /**
     * Create category
     */
    public function create(array $categoryData): array
    {
        return $this->request(
            RequestMethod::POST,
            '/sapi/product-category',
            $categoryData
        );
    }

    /**
     * Update category
     */
    public function update(int $id, array $categoryData): array
    {
        return $this->request(
            RequestMethod::PUT,
            "/sapi/product-category/{$id}",
            $categoryData
        );
    }

    /**
     * Delete category
     */
    public function delete(int $id): void
    {
        $this->request(
            RequestMethod::DELETE,
            "/sapi/product-category/{$id}"
        );
    }
}
