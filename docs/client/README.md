# Client Components

The MCP SDK provides client components for interacting with MCP servers. This section covers the main client components and their usage.

## Table of Contents

1. [MCPClient](#mcpclient)
2. [StreamingMCPClient](#streamingmcpclient)
3. [Client Configuration](#client-configuration)
4. [Error Handling](#error-handling)

## MCPClient

The `MCPClient` class provides a synchronous interface for sending messages to an MCP server.

### Basic Usage

```php
use Ronydebnath\MCP\Client\MCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$client = app(MCPClient::class);

$message = new Message(
    role: Role::USER,
    content: 'Hello, how are you?',
    type: 'text'
);

$response = $client->send($message);
```

### Methods

#### `send(Message $message): Message`

Sends a message to the server and returns the response.

```php
$response = $client->send($message);
```

#### `setConnection(string $name): self`

Sets the connection configuration to use.

```php
$client->setConnection('custom');
```

## StreamingMCPClient

The `StreamingMCPClient` class provides an asynchronous interface for streaming messages to and from an MCP server.

### Basic Usage

```php
use Ronydebnath\MCP\Client\StreamingMCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$client = app(StreamingMCPClient::class);

$message = new Message(
    role: Role::USER,
    content: 'Hello, how are you?',
    type: 'text'
);

$client->onMessage(function (Message $message) {
    // Handle incoming message
});

$client->onError(function (\Throwable $error) {
    // Handle error
});

$client->connect();
$client->send($message);
```

### Methods

#### `connect(): void`

Establishes a connection to the server.

```php
$client->connect();
```

#### `send(Message $message): void`

Sends a message to the server.

```php
$client->send($message);
```

#### `onMessage(callable $callback): self`

Sets a callback for handling incoming messages.

```php
$client->onMessage(function (Message $message) {
    // Handle message
});
```

#### `onError(callable $callback): self`

Sets a callback for handling errors.

```php
$client->onError(function (\Throwable $error) {
    // Handle error
});
```

#### `close(): void`

Closes the connection to the server.

```php
$client->close();
```

## Client Configuration

The client can be configured through the `config/mcp.php` file:

```php
return [
    'connections' => [
        'default' => [
            'host' => env('MCP_HOST', 'localhost'),
            'port' => env('MCP_PORT', 8000),
            'timeout' => env('MCP_TIMEOUT', 30),
            'verify' => env('MCP_VERIFY_SSL', true),
        ],
    ],
];
```

### Connection Options

- `host`: The server hostname
- `port`: The server port
- `timeout`: Connection timeout in seconds
- `verify`: Whether to verify SSL certificates

## Error Handling

The client components throw exceptions in various error conditions:

### Connection Errors

```php
try {
    $client->send($message);
} catch (ConnectionException $e) {
    // Handle connection error
}
```

### Authentication Errors

```php
try {
    $client->send($message);
} catch (AuthenticationException $e) {
    // Handle authentication error
}
```

### Message Errors

```php
try {
    $client->send($message);
} catch (MessageException $e) {
    // Handle message error
}
```

## Next Steps

- Learn about [Server Components](../server/README.md)
- Understand [Shared Components](../shared/README.md)
- Read about [Security](../security.md) 