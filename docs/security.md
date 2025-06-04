# Security

This document outlines security considerations and best practices when using the MCP SDK.

## Table of Contents

1. [Authentication](#authentication)
2. [Authorization](#authorization)
3. [Data Protection](#data-protection)
4. [Transport Security](#transport-security)
5. [Best Practices](#best-practices)

## Authentication

The MCP SDK provides token-based authentication through the `AuthProvider` class.

### Token Generation

```php
use Ronydebnath\MCP\Server\Auth\AuthProvider;

$auth = app(AuthProvider::class);
$token = $auth->generateToken();
```

### Token Validation

```php
$isValid = $auth->validateToken($token);
```

### Token Revocation

```php
$auth->revokeToken($token);
```

### Token Expiry

Tokens expire after a configurable period (default: 60 seconds). You can configure this in `config/mcp.php`:

```php
return [
    'auth' => [
        'token_expiry' => env('MCP_TOKEN_EXPIRY', 60),
        'token_length' => env('MCP_TOKEN_LENGTH', 32),
    ],
];
```

## Authorization

The MCP SDK provides middleware for protecting routes that require authentication.

### Route Protection

```php
use Ronydebnath\MCP\Server\Auth\AuthMiddleware;

Route::middleware(AuthMiddleware::class)->group(function () {
    Route::post('/mcp', function (Request $request, MCPServer $server) {
        return $server->handle($request);
    });
});
```

### CORS Configuration

You can configure allowed origins in `config/mcp.php`:

```php
return [
    'server' => [
        'allowed_origins' => explode(',', env('MCP_ALLOWED_ORIGINS', '*')),
    ],
];
```

## Data Protection

### Message Encryption

All messages are encrypted in transit using TLS. Make sure to:

1. Use HTTPS in production
2. Configure SSL certificates properly
3. Keep certificates up to date

### Data Storage

When using memory persistence, data is stored in the configured storage path:

```php
return [
    'memory' => [
        'storage_path' => env('MCP_MEMORY_STORAGE_PATH', storage_path('app/mcp/memory')),
    ],
];
```

Make sure to:

1. Set appropriate file permissions
2. Use a secure storage location
3. Implement proper backup procedures

## Transport Security

### SSL/TLS Configuration

Configure SSL/TLS in `config/mcp.php`:

```php
return [
    'connections' => [
        'default' => [
            'verify' => env('MCP_VERIFY_SSL', true),
        ],
    ],
];
```

### Connection Timeout

Configure connection timeout in `config/mcp.php`:

```php
return [
    'connections' => [
        'default' => [
            'timeout' => env('MCP_TIMEOUT', 30),
        ],
    ],
];
```

## Best Practices

### 1. Use Environment Variables

Always use environment variables for sensitive configuration:

```env
MCP_TOKEN_EXPIRY=60
MCP_TOKEN_LENGTH=32
MCP_VERIFY_SSL=true
```

### 2. Implement Rate Limiting

Consider implementing rate limiting for your MCP endpoints:

```php
Route::middleware(['throttle:60,1', AuthMiddleware::class])->group(function () {
    Route::post('/mcp', function (Request $request, MCPServer $server) {
        return $server->handle($request);
    });
});
```

### 3. Validate Input

Always validate input data:

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make($request->all(), [
    'message' => 'required|string|max:4096',
    'type' => 'required|string|in:text,image,audio',
]);
```

### 4. Handle Errors Securely

Implement proper error handling:

```php
try {
    $response = $server->handle($request);
} catch (AuthenticationException $e) {
    return response()->json(['error' => 'Authentication failed'], 401);
} catch (Exception $e) {
    return response()->json(['error' => 'An error occurred'], 500);
}
```

### 5. Regular Updates

Keep the MCP SDK and its dependencies up to date:

```bash
composer update ronydebnath/mcp-sdk
```

### 6. Logging

Implement proper logging for security events:

```php
use Illuminate\Support\Facades\Log;

try {
    $response = $server->handle($request);
} catch (AuthenticationException $e) {
    Log::warning('Authentication failed', [
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
    throw $e;
}
```

## Next Steps

- Learn about [Client Components](./client/README.md)
- Explore [Server Components](./server/README.md)
- Understand [Shared Components](./shared/README.md) 