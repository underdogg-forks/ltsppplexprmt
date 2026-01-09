<?php

namespace App\Services\LetsPeppol\DTOs;

/**
 * Authentication Data Transfer Object
 * 
 * Represents authentication response with JWT token
 */
class AuthenticationDTO
{
    private ?string $token = null;
    private ?string $refreshToken = null;
    private ?string $expiresAt = null;
    private ?string $tokenType = null;

    public function setToken(?string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setExpiresAt(?string $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getExpiresAt(): ?string
    {
        return $this->expiresAt;
    }

    public function setTokenType(?string $tokenType): self
    {
        $this->tokenType = $tokenType;
        return $this;
    }

    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }
}
