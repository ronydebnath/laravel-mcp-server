# Server Components

The MCP SDK provides server components for handling MCP client requests. This section covers the main server components and their usage.

## Table of Contents

1. [MCPServer](#mcpserver)
2. [AuthProvider](#authprovider)
3. [AuthMiddleware](#authmiddleware)
4. [Server Configuration](#server-configuration)
5. [Error Handling](#error-handling)

## MCPServer

The `MCPServer` class handles incoming MCP client requests and manages server-side operations.

### Basic Usage

```php
use Ronydebnath\MCP\Server\MCPServer;
use Illuminate\Http\Request;

Route::post('/mcp', function (Request $request, MCPServer $server) {
    return $server->handle($request);
});
```

### Methods

#### `handle(Request $request): Response`

Handles an incoming request and returns a response.

```php
$response = $server->handle($request);
```

#### `setSessionExpiry(int $seconds): self`

Sets the session expiry time in seconds.

```php
$server->setSessionExpiry(3600);
```

#### `setMaxConnections(int $max): self`

Sets the maximum number of concurrent connections.

```php
$server->setMaxConnections(100);
```

## AuthProvider

The `AuthProvider` class manages authentication tokens and sessions.

### Basic Usage

```php
use Ronydebnath\MCP\Server\Auth\AuthProvider;

$auth = app(AuthProvider::class);

// Generate a token
$token = $auth->generateToken();

// Validate a token
$isValid = $auth->validateToken($token);

// Revoke a token
$auth->revokeToken($token);
```

### Methods

#### `generateToken(): string`

Generates a new authentication token.

```php
$token = $auth->generateToken();
```

#### `validateToken(string $token): bool`

Validates an authentication token.

```php
$isValid = $auth->validateToken($token);
```

#### `revokeToken(string $token): void`

Revokes an authentication token.

```php
$auth->revokeToken($token);
```

#### `clearExpiredTokens(): void`

Clears expired tokens from storage.

```php
$auth->clearExpiredTokens();
```

## AuthMiddleware

The `AuthMiddleware` class provides middleware for protecting routes that require authentication.

### Basic Usage

```php
use Ronydebnath\MCP\Server\Auth\AuthMiddleware;

Route::middleware(AuthMiddleware::class)->group(function () {
    Route::post('/mcp', function (Request $request, MCPServer $server) {
        return $server->handle($request);
    });
});
```

### Configuration

The middleware can be configured in `config/mcp.php`:

```php
return [
    'auth' => [
        'token_expiry' => env('MCP_TOKEN_EXPIRY', 60),
        'token_length' => env('MCP_TOKEN_LENGTH', 32),
    ],
];
```

## Server Configuration

The server can be configured through the `config/mcp.php` file:

```php
return [
    'server' => [
        'session_expiry' => env('MCP_SESSION_EXPIRY', 60),
        'max_connections' => env('MCP_MAX_CONNECTIONS', 100),
        'allowed_origins' => explode(',', env('MCP_ALLOWED_ORIGINS', '*')),
    ],
];
```

### Server Options

- `session_expiry`: Session expiry time in seconds
- `max_connections`: Maximum number of concurrent connections
- `allowed_origins`: List of allowed CORS origins

## Error Handling

The server components throw exceptions in various error conditions:

### Authentication Errors

```php
try {
    $server->handle($request);
} catch (AuthenticationException $e) {
    // Handle authentication error
}
```

### Session Errors

```php
try {
    $server->handle($request);
} catch (SessionException $e) {
    // Handle session error
}
```

### Connection Errors

```php
try {
    $server->handle($request);
} catch (ConnectionException $e) {
    // Handle connection error
}
```

## Next Steps

- Learn about [Client Components](../client/README.md)
- Understand [Shared Components](../shared/README.md)
- Read about [Security](../security.md) 