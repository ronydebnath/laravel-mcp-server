<?php

namespace Ronydebnath\MCP\Server\Auth;

use Ronydebnath\MCP\Server\Auth\AuthenticationException;

class AuthProvider
{
    protected array $tokens = [];
    protected int $expiryTime = 3600; // 1 hour in seconds

    public function __construct(int $expiryTime = 3600)
    {
        $this->expiryTime = $expiryTime;
    }

    public function generateToken(string $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $this->tokens[$token] = [
            'user_id' => $userId,
            'created_at' => time(),
            'expires_at' => time() + $this->expiryTime
        ];
        return $token;
    }

    public function validateToken(string $token): bool
    {
        if (!isset($this->tokens[$token])) {
            throw new AuthenticationException('Token not found');
        }

        $tokenData = $this->tokens[$token];
        if (time() > $tokenData['expires_at']) {
            unset($this->tokens[$token]);
            throw new AuthenticationException('Token has expired');
        }

        return true;
    }

    public function revokeToken(string $token): void
    {
        unset($this->tokens[$token]);
    }

    public function clearExpiredTokens(): void
    {
        $now = time();
        foreach ($this->tokens as $token => $data) {
            if ($now > $data['expires_at']) {
                unset($this->tokens[$token]);
            }
        }
    }

    public function setExpiryTime(int $seconds): void
    {
        $this->expiryTime = $seconds;
    }
} 