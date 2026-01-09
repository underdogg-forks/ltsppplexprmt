<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Document Data Transfer Object
 * 
 * Represents a Peppol document (invoice, credit note, etc.)
 */
class DocumentDTO
{
    private ?string $id = null;
    private ?string $type = null;
    private ?string $direction = null;
    private ?bool $draft = null;
    private ?float $amount = null;
    private ?string $currency = null;
    private ?string $issueDate = null;
    private ?string $dueDate = null;
    private ?string $supplierId = null;
    private ?string $supplierName = null;
    private ?string $customerId = null;
    private ?string $customerName = null;
    private ?string $status = null;
    private ?bool $read = null;
    private ?bool $paid = null;

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setDirection(?string $direction): self
    {
        $this->direction = $direction;
        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDraft(?bool $draft): self
    {
        $this->draft = $draft;
        return $this;
    }

    public function getDraft(): ?bool
    {
        return $this->draft;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
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

    public function setIssueDate(?string $issueDate): self
    {
        $this->issueDate = $issueDate;
        return $this;
    }

    public function getIssueDate(): ?string
    {
        return $this->issueDate;
    }

    public function setDueDate(?string $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    public function setSupplierId(?string $supplierId): self
    {
        $this->supplierId = $supplierId;
        return $this;
    }

    public function getSupplierId(): ?string
    {
        return $this->supplierId;
    }

    public function setSupplierName(?string $supplierName): self
    {
        $this->supplierName = $supplierName;
        return $this;
    }

    public function getSupplierName(): ?string
    {
        return $this->supplierName;
    }

    public function setCustomerId(?string $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerName(?string $customerName): self
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setRead(?bool $read): self
    {
        $this->read = $read;
        return $this;
    }

    public function getRead(): ?bool
    {
        return $this->read;
    }

    public function setPaid(?bool $paid): self
    {
        $this->paid = $paid;
        return $this;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }
}
