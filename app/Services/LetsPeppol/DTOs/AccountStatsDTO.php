<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Account Statistics Data Transfer Object
 * 
 * Represents account usage statistics
 */
class AccountStatsDTO
{
    private ?int $totalDocuments = null;
    private ?int $incomingDocuments = null;
    private ?int $outgoingDocuments = null;
    private ?int $draftDocuments = null;
    private ?float $totalAmount = null;
    private ?string $currency = null;
    private ?string $lastActivityAt = null;

    public function setTotalDocuments(?int $totalDocuments): self
    {
        $this->totalDocuments = $totalDocuments;
        return $this;
    }

    public function getTotalDocuments(): ?int
    {
        return $this->totalDocuments;
    }

    public function setIncomingDocuments(?int $incomingDocuments): self
    {
        $this->incomingDocuments = $incomingDocuments;
        return $this;
    }

    public function getIncomingDocuments(): ?int
    {
        return $this->incomingDocuments;
    }

    public function setOutgoingDocuments(?int $outgoingDocuments): self
    {
        $this->outgoingDocuments = $outgoingDocuments;
        return $this;
    }

    public function getOutgoingDocuments(): ?int
    {
        return $this->outgoingDocuments;
    }

    public function setDraftDocuments(?int $draftDocuments): self
    {
        $this->draftDocuments = $draftDocuments;
        return $this;
    }

    public function getDraftDocuments(): ?int
    {
        return $this->draftDocuments;
    }

    public function setTotalAmount(?float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
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

    public function setLastActivityAt(?string $lastActivityAt): self
    {
        $this->lastActivityAt = $lastActivityAt;
        return $this;
    }

    public function getLastActivityAt(): ?string
    {
        return $this->lastActivityAt;
    }
}
