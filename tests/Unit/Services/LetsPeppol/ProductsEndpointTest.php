<?php

namespace Tests\Unit\Services\LetsPeppol;

use App\Services\LetsPeppol\Endpoints\App\ProductsEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;
use App\Services\LetsPeppol\Testing\FakeClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProductsEndpoint::class)]
class ProductsEndpointTest extends TestCase
{
    private FakeClient $fakeClient;
    private ProductsEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fakeClient = new FakeClient();
        $this->endpoint = new ProductsEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_lists_all_products(): void
    {
        // Arrange
        $expectedProducts = [
            [
                'id' => 1,
                'name' => 'Laptop',
                'price' => 999.99,
            ],
        ];
        $this->fakeClient->queueResponse($expectedProducts);

        // Act
        $result = $this->endpoint->list();

        // Assert
        $this->assertEquals($expectedProducts, $result);
        $this->fakeClient->assertRequestSent('/sapi/product', RequestMethod::GET);
    }

    #[Test]
    public function it_creates_product(): void
    {
        // Arrange
        $productData = [
            'name' => 'Laptop',
            'price' => 999.99,
            'sku' => 'LAPTOP-001',
        ];
        $expectedResponse = [
            'id' => 1,
            ...$productData,
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->create($productData);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent('/sapi/product', RequestMethod::POST);
        $this->fakeClient->assertRequestSentWithData($productData);
    }

    #[Test]
    public function it_updates_product(): void
    {
        // Arrange
        $productId = 1;
        $productData = [
            'name' => 'Updated Laptop',
            'price' => 899.99,
        ];
        $expectedResponse = [
            'id' => $productId,
            ...$productData,
        ];
        $this->fakeClient->queueResponse($expectedResponse);

        // Act
        $result = $this->endpoint->update($productId, $productData);

        // Assert
        $this->assertEquals($expectedResponse, $result);
        $this->fakeClient->assertRequestSent("/sapi/product/{$productId}", RequestMethod::PUT);
    }

    #[Test]
    public function it_deletes_product(): void
    {
        // Arrange
        $productId = 1;
        $this->fakeClient->queueResponse(null);

        // Act
        $this->endpoint->delete($productId);

        // Assert
        $this->fakeClient->assertRequestSent("/sapi/product/{$productId}", RequestMethod::DELETE);
        $this->fakeClient->assertRequestCount(1);
    }
}
