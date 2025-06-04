# Shared Components

The MCP SDK provides shared components that are used by both client and server components. This section covers the main shared components and their usage.

## Table of Contents

1. [Memory](#memory)
2. [Progress](#progress)
3. [Context](#context)
4. [Types](#types)
5. [Configuration](#configuration)

## Memory

The `Memory` class provides a way to store and manage messages in memory.

### Basic Usage

```php
use Ronydebnath\MCP\Shared\Memory;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$memory = app(Memory::class);

// Add a message
$memory->add(new Message(
    role: Role::USER,
    content: 'Hello',
    type: 'text'
));

// Get all messages
$messages = $memory->getMessages();

// Get messages by role
$userMessages = $memory->getMessagesByRole(Role::USER);

// Get messages by type
$textMessages = $memory->getMessagesByType('text');
```

### Methods

#### `add(Message $message): void`

Adds a message to memory.

```php
$memory->add($message);
```

#### `getMessages(): array`

Gets all messages from memory.

```php
$messages = $memory->getMessages();
```

#### `getMessagesByRole(Role $role): array`

Gets messages by role.

```php
$userMessages = $memory->getMessagesByRole(Role::USER);
```

#### `getMessagesByType(string $type): array`

Gets messages by type.

```php
$textMessages = $memory->getMessagesByType('text');
```

#### `clear(): void`

Clears all messages from memory.

```php
$memory->clear();
```

## Progress

The `Progress` class provides a way to track progress of operations.

### Basic Usage

```php
use Ronydebnath\MCP\Shared\Progress;

$progress = app(Progress::class);

// Update progress
$progress->update(50, 'Processing...', ['step' => 'analysis']);

// Get progress information
$percentage = $progress->getPercentage();
$status = $progress->getStatus();
$metadata = $progress->getMetadata();
```

### Methods

#### `update(float $current, ?string $status = null, ?array $metadata = null): void`

Updates the progress.

```php
$progress->update(50, 'Processing...', ['step' => 'analysis']);
```

#### `increment(float $amount = 1.0): void`

Increments the progress by the specified amount.

```php
$progress->increment(10);
```

#### `getPercentage(): float`

Gets the progress percentage.

```php
$percentage = $progress->getPercentage();
```

#### `getStatus(): ?string`

Gets the progress status.

```php
$status = $progress->getStatus();
```

#### `getMetadata(): ?array`

Gets the progress metadata.

```php
$metadata = $progress->getMetadata();
```

#### `reset(): void`

Resets the progress.

```php
$progress->reset();
```

## Context

The `Context` class provides a way to store and manage context data.

### Basic Usage

```php
use Ronydebnath\MCP\Shared\Context;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$context = app(Context::class);

// Set context data
$context->set('user_id', 123);
$context->set('preferences', ['theme' => 'dark']);

// Get context data
$userId = $context->get('user_id');
$preferences = $context->get('preferences');

// Add a message to context
$context->addMessage(new Message(
    role: Role::USER,
    content: 'Hello',
    type: 'text'
));
```

### Methods

#### `set(string $key, mixed $value): void`

Sets a context value.

```php
$context->set('key', 'value');
```

#### `get(string $key, mixed $default = null): mixed`

Gets a context value.

```php
$value = $context->get('key', 'default');
```

#### `has(string $key): bool`

Checks if a context key exists.

```php
$exists = $context->has('key');
```

#### `remove(string $key): void`

Removes a context value.

```php
$context->remove('key');
```

#### `clear(): void`

Clears all context data.

```php
$context->clear();
```

#### `addMessage(Message $message): void`

Adds a message to context.

```php
$context->addMessage($message);
```

#### `getMessages(): array`

Gets all messages from context.

```php
$messages = $context->getMessages();
```

#### `clearMessages(): void`

Clears all messages from context.

```php
$context->clearMessages();
```

## Types

The MCP SDK provides several types for working with messages and roles.

### Message

```php
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

$message = new Message(
    role: Role::USER,
    content: 'Hello',
    type: 'text'
);
```

### Role

```php
use Ronydebnath\MCP\Types\Role;

// Available roles
Role::USER;
Role::ASSISTANT;
Role::SYSTEM;
```

## Configuration

The shared components can be configured through the `config/mcp.php` file:

```php
return [
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

### Memory Options

- `max_size`: Maximum number of messages to store
- `persist`: Whether to persist messages to storage
- `storage_path`: Path to store persisted messages

### Message Options

- `default_type`: Default message type
- `max_length`: Maximum message length

## Next Steps

- Learn about [Client Components](../client/README.md)
- Explore [Server Components](../server/README.md)
- Read about [Security](../security.md) 