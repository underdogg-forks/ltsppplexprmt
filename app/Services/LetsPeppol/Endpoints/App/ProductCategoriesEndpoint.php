<?php

namespace App\Services\LetsPeppol\Endpoints\App;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Product Categories endpoint client (AppService)
 * 
 * Namespace: App
 * Base URL: /sapi/product-category
 */
class ProductCategoriesEndpoint extends BaseEndpoint
{
    /**
     * List root categories
     * 
     * Response:
     * [
     *   {
     *     "id": 1,
     *     "name": "Electronics",
     *     "parentId": null,
     *     "children": [...]
     *   }
     * ]
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
     * 
     * Response:
     * [
     *   {"id": 1, "name": "Electronics", "parentId": null},
     *   {"id": 2, "name": "Laptops", "parentId": 1}
     * ]
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
     * 
     * Response:
     * {
     *   "id": 1,
     *   "name": "Electronics",
     *   "parentId": null,
     *   "children": [...]
     * }
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
     * 
     * Request:
     * {
     *   "name": "Electronics",
     *   "parentId": null
     * }
     * 
     * Response:
     * {
     *   "id": 1,
     *   "name": "Electronics",
     *   "parentId": null
     * }
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
     * 
     * Request:
     * {
     *   "name": "Consumer Electronics",
     *   "parentId": null
     * }
     * 
     * Response:
     * {
     *   "id": 1,
     *   "name": "Consumer Electronics",
     *   "parentId": null
     * }
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
