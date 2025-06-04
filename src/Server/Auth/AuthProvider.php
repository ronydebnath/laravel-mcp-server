<?php

namespace Ronydebnath\MCP\Server\Auth;

class AuthProvider
{
    private array $tokens = [];

    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $this->tokens[$token] = time() + 3600; // 1 hour expiry
        return $token;
    }

    public function validateToken(string $token): bool
    {
        return isset($this->tokens[$token]) && $this->tokens[$token] > time();
    }

    public function revokeToken(string $token): void
    {
        unset($this->tokens[$token]);
    }

    public function clearExpiredTokens(): void
    {
        $now = time();
        foreach ($this->tokens as $token => $expiry) {
            if ($expiry < $now) {
                unset($this->tokens[$token]);
            }
        }
    }
} 