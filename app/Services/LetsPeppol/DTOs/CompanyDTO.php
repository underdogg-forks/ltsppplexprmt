<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Company Data Transfer Object
 * 
 * Represents company information in the LetsPeppol system
 */
class CompanyDTO
{
    private ?string $peppolId = null;
    private ?string $name = null;
    private ?string $vatNumber = null;
    private ?string $email = null;
    private ?string $address = null;
    private ?string $city = null;
    private ?string $postalCode = null;
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

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
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

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
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

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
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
