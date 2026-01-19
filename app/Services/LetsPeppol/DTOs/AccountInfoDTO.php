<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Account Info Data Transfer Object
 * 
 * Represents user account information
 */
class AccountInfoDTO
{
    private ?string $id = null;
    private ?string $email = null;
    private ?string $name = null;
    private ?string $firstName = null;
    private ?string $lastName = null;
    private ?string $companyId = null;
    private array $roles = [];
    private ?bool $active = null;

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setCompanyId(?string $companyId): self
    {
        $this->companyId = $companyId;
        return $this;
    }

    public function getCompanyId(): ?string
    {
        return $this->companyId;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
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
}
