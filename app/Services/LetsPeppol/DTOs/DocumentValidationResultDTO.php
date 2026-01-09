<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Document Validation Result Data Transfer Object
 * 
 * Represents the result of UBL XML validation
 */
class DocumentValidationResultDTO
{
    private ?bool $valid = null;
    private array $errors = [];
    private array $warnings = [];

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;
        return $this;
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setWarnings(array $warnings): self
    {
        $this->warnings = $warnings;
        return $this;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function addError(string $error): self
    {
        $this->errors[] = $error;
        return $this;
    }

    public function addWarning(string $warning): self
    {
        $this->warnings[] = $warning;
        return $this;
    }
}
