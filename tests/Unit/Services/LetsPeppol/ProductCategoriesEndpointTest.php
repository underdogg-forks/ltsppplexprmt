<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\App\ProductCategoriesEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProductCategoriesEndpoint::class)]
class ProductCategoriesEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private ProductCategoriesEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new ProductCategoriesEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_lists_root_categories(): void
    {
        // Arrange
        $expectedCategories = [
            [
                'id' => 1,
                'name' => 'Electronics',
                'parentId' => null,
            ],
        ];
        $this->fakeClient->queueResponse($expectedCategories);

        // Act
        $result = $this->endpoint->listRoot();

        // Assert
        $this->assertEquals($expectedCategories, $result);
        $this->fakeClient->assertRequestSent('/sapi/product-category', RequestMethod::GET);
    }

    #[Test]
    public function it_lists_root_categories_with_deep_hierarchy(): void
    {
        // Arrange
        $this->fakeClient->queueResponse([]);

        // Act
        $this->endpoint->listRoot(true);

        // Assert
        $this->fakeClient->assertRequestSent('/sapi/product-category', RequestMethod::GET);
    }

    #[Test]
    public function it_lists_all_categories_flat(): void
    {
        // Arrange
        $expectedCategories = [
            ['id' => 1, 'name' => 'Electronics', 'parentId' => null],
            ['id' => 2, 'name' => 'Laptops', 'parentId' => 1],
        ];
        $this->fakeClient->queueResponse($expectedCategories);

        // Act
        $result = $this->endpoint->listAll();

        // Assert
        $this->assertEquals($expectedCategories, $result);
        $this->fakeClient->assertRequestSent('/sapi/product-category/all', RequestMethod::GET);
    }

    #[Test]
    public function it_retrieves_category_by_id(): void
    {
        // Arrange
        $categoryId = 1;
        $expectedCategory = [
            'id' => $categoryId,
            'name' => 'Electronics',
            'parentId' => null,
        ];
        $this->fakeClient->queueResponse($expectedCategory);

        // Act
        $result = $this->endpoint->get($categoryId);

        // Assert
        $this->assertEquals($expectedCategory, $result);
        $this->fakeClient->assertRequestSent("/sapi/product-category/{$categoryId}", RequestMethod::GET);
    }

    #[Test]
    public function it_creates_category(): void
    {
        // Arrange
        $categoryData = [
            'name' => 'Electronics',
            'parentId' => null,
        ];
        $expectedResponse = [
            'id' => 1,
            ...$categoryData,
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->create($categoryData);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent('/sapi/product-category', RequestMethod::POST);
        $this->fakeClient->assertRequestSentWithData($categoryData);
    }

    #[Test]
    public function it_updates_category(): void
    {
        // Arrange
        $categoryId = 1;
        $categoryData = [
            'name' => 'Consumer Electronics',
        ];
        $expectedResponse = [
            'id' => $categoryId,
            ...$categoryData,
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->update($categoryId, $categoryData);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent("/sapi/product-category/{$categoryId}", RequestMethod::PUT);
    }

    #[Test]
    public function it_deletes_category(): void
    {
        // Arrange
        $categoryId = 1;
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->delete($categoryId);

        // Assert
        $this->fakeClient->assertRequestSent("/sapi/product-category/{$categoryId}", RequestMethod::DELETE);
        $this->fakeClient->assertRequestCount(1);
    }
}
