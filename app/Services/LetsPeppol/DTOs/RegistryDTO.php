<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Registry Data Transfer Object
 * 
 * Represents a Peppol registry entry
 */
class RegistryDTO
{
    private ?string $peppolId = null;
    private ?string $companyName = null;
    private ?string $registeredAt = null;
    private ?bool $active = null;
    private ?string $country = null;

    public function setPeppolId(?string $peppolId): self
    {
        $this->peppolId = $peppolId;
        return $this;
    }

    public function getPeppolId(): ?string
    {
        return $this->peppolId;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setRegisteredAt(?string $registeredAt): self
    {
        $this->registeredAt = $registeredAt;
        return $this;
    }

    public function getRegisteredAt(): ?string
    {
        return $this->registeredAt;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }
}
