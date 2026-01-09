<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Product Category Data Transfer Object
 * 
 * Represents a product category with hierarchical support
 */
class ProductCategoryDTO
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $description = null;
    private ?int $parentId = null;
    private array $children = [];

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

    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;
        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setChildren(array $children): self
    {
        $this->children = $children;
        return $this;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        $this->children[] = $child;
        return $this;
    }
}
