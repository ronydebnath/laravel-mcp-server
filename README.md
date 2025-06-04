[![Latest Version on Packagist](https://img.shields.io/packagist/v/ronydebnath/mcp-sdk.svg)](https://packagist.org/packages/ronydebnath/mcp-sdk)

# MCP SDK for Laravel

A Laravel package for interacting with Model Context Protocol (MCP) servers and implementing MCP servers.

## Installation

You can install the package via composer:

```bash
composer require ronydebnath/mcp-sdk
```

The package will automatically register its service provider.

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Ronydebnath\MCP\MCPServiceProvider" --tag="config"
```

This will create a `config/mcp.php` file in your config directory.

## Configuration

The package configuration file includes settings for both client and server functionality:

```php
return [
    'default' => env('MCP_CONNECTION', 'default'),
    
    'connections' => [
        'default' => [
            'host' => env('MCP_HOST', 'localhost'),
            'port' => env('MCP_PORT', 8000),
            'timeout' => env('MCP_TIMEOUT', 30),
            'verify' => env('MCP_VERIFY_SSL', true),
        ],
    ],

    'server' => [
        'session_expiry' => env('MCP_SESSION_EXPIRY', 60),
        'max_connections' => env('MCP_MAX_CONNECTIONS', 100),
        'allowed_origins' => explode(',', env('MCP_ALLOWED_ORIGINS', '*')),
    ],

    'auth' => [
        'token_expiry' => env('MCP_TOKEN_EXPIRY', 60),
        'token_length' => env('MCP_TOKEN_LENGTH', 32),
    ],

    'memory' => [
        'max_size' => env('MCP_MEMORY_MAX_SIZE', 100),
        'persist' => env('MCP_MEMORY_PERSIST', false),
        'storage_path' => env('MCP_MEMORY_STORAGE_PATH', storage_path('app/mcp/memory')),
    ],

    'message' => [
        'default_type' => 'text',
        'max_length' => env('MCP_MAX_MESSAGE_LENGTH', 4096),
    ],
];
```

## Using the MCP Client

### Using Dependency Injection

```php
use Ronydebnath\MCP\Client\MCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

class YourController
{
    public function __construct(private MCPClient $client)
    {}

    public function handle()
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello, how are you?',
            type: 'text'
        );

        $response = $this->client->send($message);
    }
}
```

### Using the Facade

```php
use Ronydebnath\MCP\Facades\MCP;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$message = new Message(
    role: Role::USER,
    content: 'Hello, how are you?',
    type: 'text'
);

$response = MCP::send($message);
```

### Sending Multiple Messages

```php
use Ronydebnath\MCP\Client\MCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$messages = [
    new Message(role: Role::USER, content: 'First message', type: 'text'),
    new Message(role: Role::USER, content: 'Second message', type: 'text'),
];

$responses = $client->sendMultiple($messages);
```

### Using Streaming

```php
use Ronydebnath\MCP\Client\StreamingMCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

// Using Dependency Injection
class YourController
{
    public function __construct(private StreamingMCPClient $client)
    {}

    public function handle()
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello, how are you?',
            type: 'text'
        );

        $this->client->stream($message, function ($chunk) {
            // Handle each chunk of the response
            echo $chunk['content'];
        });
    }
}

// Using the Facade
MCP::stream($message, function ($chunk) {
    echo $chunk['content'];
});

// Streaming multiple messages
$messages = [
    new Message(role: Role::USER, content: 'First message', type: 'text'),
    new Message(role: Role::USER, content: 'Second message', type: 'text'),
];

$client->streamMultiple($messages, function ($chunk) {
    echo $chunk['content'];
});
```

## Using the MCP Server

### Setting Up Routes

Add the following route to your `routes/api.php`:

```php
use Ronydebnath\MCP\Server\MCPServer;

Route::post('/mcp', function (Request $request, MCPServer $server) {
    return $server->handle($request);
});
```

### Registering Message Handlers

```php
use Ronydebnath\MCP\Server\MCPServer;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$server->registerHandler('text', function (Message $message) {
    return [
        'role' => Role::ASSISTANT->value,
        'content' => 'This is a response to: ' . $message->content,
        'type' => 'text',
    ];
});
```

### Using Authentication

The package includes built-in authentication support:

```php
use Ronydebnath\MCP\Server\Auth\AuthMiddleware;
use Ronydebnath\MCP\Server\Auth\AuthProvider;

// Generate a token
$token = $authProvider->generateToken();

// Register the authentication middleware
$server->registerMiddleware(function ($request) use ($authMiddleware) {
    return $authMiddleware->handle($request);
});
```

Clients can authenticate using either:
1. Bearer token in the Authorization header:
```
Authorization: Bearer your-token-here
```

2. Token as a query parameter:
```
/mcp?token=your-token-here
```

### Handling Streaming Requests

The server automatically handles streaming requests when the client sets the `Accept: text/event-stream` header. The server will:

1. Create a new session or use an existing one
2. Send an initial connection message
3. Stream response chunks as they become available
4. Maintain the session for the duration of the connection

## Memory and Context Management

The package includes memory and context management for maintaining conversation state:

### Using Memory

```php
use Ronydebnath\MCP\Shared\Memory;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$memory = app(Memory::class);

// Add messages to memory
$memory->add(new Message(
    role: Role::USER,
    content: 'Hello',
    type: 'text'
));

// Get messages by role
$userMessages = $memory->getByRole(Role::USER);

// Get messages by type
$textMessages = $memory->getByType('text');

// Clear memory
$memory->clear();
```

### Using Context

```php
use Ronydebnath\MCP\Shared\Context;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$context = app(Context::class);

// Set context data
$context->set('user_id', 123);
$context->set('preferences', ['theme' => 'dark']);

// Add messages to context
$context->addMessage(new Message(
    role: Role::USER,
    content: 'Hello',
    type: 'text'
));

// Get context data
$userId = $context->get('user_id');
$messages = $context->getMessages();

// Clear context
$context->clear();
```

### Using Progress Tracking

```php
use Ronydebnath\MCP\Shared\Progress;

$progress = app(Progress::class);

// Update progress
$progress->update(50, 'Processing...', ['step' => 'analysis']);

// Increment progress
$progress->increment(10, 'Moving to next step');

// Get progress information
$percentage = $progress->getPercentage();
$status = $progress->getStatus();
$metadata = $progress->getMetadata();

// Check if complete
if ($progress->isComplete()) {
    // Handle completion
}

// Reset progress
$progress->reset();
```

## Testing

```bash
composer test
```

## Security

If you discover any security related issues, please email hello[at]ronydebnath.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 