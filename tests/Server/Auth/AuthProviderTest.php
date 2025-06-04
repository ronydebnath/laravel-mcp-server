<?php

namespace Ronydebnath\MCP\Tests\Server\Auth;

use PHPUnit\Framework\TestCase;
use Ronydebnath\MCP\Server\Auth\AuthProvider;
use Ronydebnath\MCP\Server\Auth\AuthenticationException;

class AuthProviderTest extends TestCase
{
    private AuthProvider $authProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authProvider = new AuthProvider([
            'token_expiry' => 60,
            'token_length' => 32,
        ]);
    }

    public function test_can_generate_token(): void
    {
        $token = $this->authProvider->generateToken();
        
        $this->assertIsString($token);
        $this->assertEquals(32, strlen($token));
    }

    public function test_can_validate_valid_token(): void
    {
        $token = $this->authProvider->generateToken();
        
        $this->assertTrue($this->authProvider->validateToken($token));
    }

    public function test_rejects_invalid_token(): void
    {
        $this->assertFalse($this->authProvider->validateToken('invalid_token'));
    }

    public function test_token_expires_after_expiry_time(): void
    {
        $this->authProvider = new AuthProvider([
            'token_expiry' => 1, // 1 second expiry
            'token_length' => 32,
        ]);
        
        $token = $this->authProvider->generateToken();
        
        // Wait for token to expire
        sleep(2);
        
        $this->assertFalse($this->authProvider->validateToken($token));
    }

    public function test_can_revoke_token(): void
    {
        $token = $this->authProvider->generateToken();
        
        $this->assertTrue($this->authProvider->validateToken($token));
        
        $this->authProvider->revokeToken($token);
        
        $this->assertFalse($this->authProvider->validateToken($token));
    }

    public function test_can_clear_expired_tokens(): void
    {
        $this->authProvider = new AuthProvider([
            'token_expiry' => 1, // 1 second expiry
            'token_length' => 32,
        ]);
        
        $token = $this->authProvider->generateToken();
        
        // Wait for token to expire
        sleep(2);
        
        $this->authProvider->clearExpiredTokens();
        
        $this->assertFalse($this->authProvider->validateToken($token));
    }

    public function test_throws_exception_when_token_not_found(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Token not found');
        
        $this->authProvider->revokeToken('non_existent_token');
    }
} 