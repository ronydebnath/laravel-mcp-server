<?php

namespace Ronydebnath\MCP\Server\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ronydebnath\MCP\Exceptions\AuthenticationException;

class AuthMiddleware
{
    public function __construct(
        private readonly AuthProvider $authProvider
    ) {}

    /**
     * Handle the incoming request
     *
     * @param Request $request
     * @return Response|null
     * @throws AuthenticationException
     */
    public function handle(Request $request): ?Response
    {
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            throw new AuthenticationException('No authentication token provided');
        }

        if (!$this->authProvider->validateToken($token)) {
            throw new AuthenticationException('Invalid authentication token');
        }

        return null;
    }

    /**
     * Get the authentication token from the request
     *
     * @param Request $request
     * @return string|null
     */
    private function getTokenFromRequest(Request $request): ?string
    {
        // Check Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        // Check query parameter
        return $request->query('token');
    }
} 