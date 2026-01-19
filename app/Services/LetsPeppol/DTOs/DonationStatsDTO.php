<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Donation Statistics Data Transfer Object
 * 
 * Represents donation statistics for the platform
 */
class DonationStatsDTO
{
    private ?int $totalDonations = null;
    private ?float $totalAmount = null;
    private ?string $currency = null;
    private ?int $uniqueDonors = null;
    private ?float $averageDonation = null;

    public function setTotalDonations(?int $totalDonations): self
    {
        $this->totalDonations = $totalDonations;
        return $this;
    }

    public function getTotalDonations(): ?int
    {
        return $this->totalDonations;
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

    public function setUniqueDonors(?int $uniqueDonors): self
    {
        $this->uniqueDonors = $uniqueDonors;
        return $this;
    }

    public function getUniqueDonors(): ?int
    {
        return $this->uniqueDonors;
    }

    public function setAverageDonation(?float $averageDonation): self
    {
        $this->averageDonation = $averageDonation;
        return $this;
    }

    public function getAverageDonation(): ?float
    {
        return $this->averageDonation;
    }
}
