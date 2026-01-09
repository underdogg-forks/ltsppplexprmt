<?php

namespace App\Services\LetsPeppol;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Decorators\HttpExceptionHandler;
use App\Services\LetsPeppol\Decorators\RequestLogger;
use App\Services\LetsPeppol\Endpoints\CompanyEndpoint;
use App\Services\LetsPeppol\Endpoints\DocumentsEndpoint;
use App\Services\LetsPeppol\Endpoints\PartnersEndpoint;
use App\Services\LetsPeppol\Endpoints\PeppolDirectoryEndpoint;
use App\Services\LetsPeppol\Endpoints\ProductCategoriesEndpoint;
use App\Services\LetsPeppol\Endpoints\ProductsEndpoint;
use App\Services\LetsPeppol\Endpoints\StatisticsEndpoint;

/**
 * App API Service Client
 * 
 * Provides access to application management endpoints
 */
class AppService
{
    protected ClientInterface $client;
    protected DocumentsEndpoint $documents;
    protected CompanyEndpoint $company;
    protected PartnersEndpoint $partners;
    protected ProductsEndpoint $products;
    protected ProductCategoriesEndpoint $productCategories;
    protected StatisticsEndpoint $statistics;
    protected PeppolDirectoryEndpoint $peppolDirectory;

    public function __construct(?string $baseUrl = null)
    {
        $baseUrl = $baseUrl ?? config('services.letspeppol.app_url', 'https://app.letspeppol.org');
        
        // Build decorator chain: RequestLogger → HttpExceptionHandler → HttpClient
        $this->client = new RequestLogger(
            new HttpExceptionHandler(
                new HttpClient($baseUrl)
            )
        );

        // Initialize endpoint clients
        $this->documents = new DocumentsEndpoint($this->client);
        $this->company = new CompanyEndpoint($this->client);
        $this->partners = new PartnersEndpoint($this->client);
        $this->products = new ProductsEndpoint($this->client);
        $this->productCategories = new ProductCategoriesEndpoint($this->client);
        $this->statistics = new StatisticsEndpoint($this->client);
        $this->peppolDirectory = new PeppolDirectoryEndpoint($this->client);
    }

    /**
     * Get documents endpoint
     */
    public function documents(): DocumentsEndpoint
    {
        return $this->documents;
    }

    /**
     * Get company endpoint
     */
    public function company(): CompanyEndpoint
    {
        return $this->company;
    }

    /**
     * Get partners endpoint
     */
    public function partners(): PartnersEndpoint
    {
        return $this->partners;
    }

    /**
     * Get products endpoint
     */
    public function products(): ProductsEndpoint
    {
        return $this->products;
    }

    /**
     * Get product categories endpoint
     */
    public function productCategories(): ProductCategoriesEndpoint
    {
        return $this->productCategories;
    }

    /**
     * Get statistics endpoint
     */
    public function statistics(): StatisticsEndpoint
    {
        return $this->statistics;
    }

    /**
     * Get Peppol Directory endpoint
     */
    public function peppolDirectory(): PeppolDirectoryEndpoint
    {
        return $this->peppolDirectory;
    }

    /**
     * Set JWT token for all endpoints
     */
    public function setToken(string $token): static
    {
        $this->client->setToken($token);
        return $this;
    }

    /**
     * Get JWT token
     */
    public function getToken(): ?string
    {
        return $this->client->getToken();
    }
}
