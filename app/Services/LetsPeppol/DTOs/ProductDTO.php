<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Product Data Transfer Object
 * 
 * Represents a product/service in the system
 */
class ProductDTO
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $description = null;
    private ?float $price = null;
    private ?string $currency = null;
    private ?float $taxRate = null;
    private ?string $unit = null;
    private ?int $categoryId = null;

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setTaxRate(?float $taxRate): self
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;
        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setCategoryId(?int $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }
}
