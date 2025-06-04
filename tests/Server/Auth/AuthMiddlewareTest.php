<?php

namespace Ronydebnath\MCP\Tests\Server\Auth;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Ronydebnath\MCP\Exceptions\AuthenticationException;
use Ronydebnath\MCP\Server\Auth\AuthMiddleware;
use Ronydebnath\MCP\Server\Auth\AuthProvider;

class AuthMiddlewareTest extends TestCase
{
    private AuthMiddleware $middleware;
    private AuthProvider $authProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authProvider = new AuthProvider();
        $this->middleware = new AuthMiddleware($this->authProvider);
    }

    public function test_validates_token_from_authorization_header(): void
    {
        $token = $this->authProvider->generateToken();
        $request = $this->createRequest($token);

        $response = $this->middleware->handle($request);
        $this->assertNull($response);
    }

    public function test_validates_token_from_query_parameter(): void
    {
        $token = $this->authProvider->generateToken();
        $request = $this->createRequest(null, $token);

        $response = $this->middleware->handle($request);
        $this->assertNull($response);
    }

    public function test_throws_exception_when_no_token_provided(): void
    {
        $request = $this->createRequest();

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('No authentication token provided');

        $this->middleware->handle($request);
    }

    public function test_throws_exception_when_invalid_token_provided(): void
    {
        $request = $this->createRequest('invalid-token');

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid authentication token');

        $this->middleware->handle($request);
    }

    private function createRequest(?string $authHeader = null, ?string $queryToken = null): Request
    {
        $request = Request::create('/mcp', 'POST');
        
        if ($authHeader) {
            $request->headers->set('Authorization', 'Bearer ' . $authHeader);
        }

        if ($queryToken) {
            $request->query->set('token', $queryToken);
        }

        return $request;
    }
} 