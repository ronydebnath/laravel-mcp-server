# Getting Started

This guide will help you get started with the MCP SDK for Laravel.

## Installation

You can install the package via composer:

```bash
composer require ronydebnath/mcp-sdk
```

The package will automatically register its service provider.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Ronydebnath\MCP\MCPServiceProvider" --tag="config"
```

This will create a `config/mcp.php` file in your config directory with the following structure:

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

## Environment Variables

Add the following to your `.env` file:

```env
MCP_CONNECTION=default
MCP_HOST=localhost
MCP_PORT=8000
MCP_TIMEOUT=30
MCP_VERIFY_SSL=true

MCP_SESSION_EXPIRY=60
MCP_MAX_CONNECTIONS=100
MCP_ALLOWED_ORIGINS=*

MCP_TOKEN_EXPIRY=60
MCP_TOKEN_LENGTH=32

MCP_MEMORY_MAX_SIZE=100
MCP_MEMORY_PERSIST=false
MCP_MEMORY_STORAGE_PATH=storage/app/mcp/memory

MCP_MAX_MESSAGE_LENGTH=4096
```

## Basic Usage

### Using the Client

```php
use Ronydebnath\MCP\Client\MCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

// Using Dependency Injection
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

// Using the Facade
use Ronydebnath\MCP\Facades\MCP;

$message = new Message(
    role: Role::USER,
    content: 'Hello, how are you?',
    type: 'text'
);

$response = MCP::send($message);
```

### Using the Server

Add the following route to your `routes/api.php`:

```php
use Ronydebnath\MCP\Server\MCPServer;

Route::post('/mcp', function (Request $request, MCPServer $server) {
    return $server->handle($request);
});
```

### Using Memory and Context

```php
use Ronydebnath\MCP\Shared\Memory;
use Ronydebnath\MCP\Shared\Context;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$memory = app(Memory::class);
$context = app(Context::class);

// Add messages to memory
$memory->add(new Message(
    role: Role::USER,
    content: 'Hello',
    type: 'text'
));

// Set context data
$context->set('user_id', 123);
$context->set('preferences', ['theme' => 'dark']);
```

### Using Progress Tracking

```php
use Ronydebnath\MCP\Shared\Progress;

$progress = app(Progress::class);

// Update progress
$progress->update(50, 'Processing...', ['step' => 'analysis']);

// Get progress information
$percentage = $progress->getPercentage();
$status = $progress->getStatus();
```

## Next Steps

- Learn more about [Client Components](./client/README.md)
- Explore [Server Components](./server/README.md)
- Understand [Shared Components](./shared/README.md)
- Read about [Security](./security.md) 