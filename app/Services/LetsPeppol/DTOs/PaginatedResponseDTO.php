<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Paginated Response Data Transfer Object
 * 
 * Generic wrapper for paginated API responses
 */
class PaginatedResponseDTO
{
    private array $content = [];
    private ?int $totalElements = null;
    private ?int $totalPages = null;
    private ?int $number = null;
    private ?int $size = null;

    public function setContent(array $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setTotalElements(?int $totalElements): self
    {
        $this->totalElements = $totalElements;
        return $this;
    }

    public function getTotalElements(): ?int
    {
        return $this->totalElements;
    }

    public function setTotalPages(?int $totalPages): self
    {
        $this->totalPages = $totalPages;
        return $this;
    }

    public function getTotalPages(): ?int
    {
        return $this->totalPages;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }
}
