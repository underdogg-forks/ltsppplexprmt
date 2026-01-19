<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Partner Data Transfer Object
 * 
 * Represents a business partner in the LetsPeppol system
 */
class PartnerDTO
{
    private ?int $id = null;
    private ?string $peppolId = null;
    private ?string $name = null;
    private ?string $email = null;
    private ?string $vatNumber = null;
    private ?string $address = null;
    private ?string $country = null;

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setPeppolId(?string $peppolId): self
    {
        $this->peppolId = $peppolId;
        return $this;
    }

    public function getPeppolId(): ?string
    {
        return $this->peppolId;
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

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setVatNumber(?string $vatNumber): self
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
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
