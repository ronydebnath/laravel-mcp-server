# MCP SDK Documentation

Welcome to the MCP SDK documentation. This documentation provides comprehensive information about the Model Context Protocol (MCP) SDK for Laravel.

## Table of Contents

1. [Getting Started](./getting-started.md)
   - Installation
   - Configuration
   - Basic Usage

2. [Client Components](./client/README.md)
   - [MCPClient](./client/mcp-client.md)
   - [StreamingMCPClient](./client/streaming-mcp-client.md)

3. [Server Components](./server/README.md)
   - [MCPServer](./server/mcp-server.md)
   - [Authentication](./server/auth/README.md)
     - [AuthProvider](./server/auth/auth-provider.md)
     - [AuthMiddleware](./server/auth/auth-middleware.md)

4. [Shared Components](./shared/README.md)
   - [Memory](./shared/memory.md)
   - [Progress](./shared/progress.md)
   - [Context](./shared/context.md)

5. [Types](./types/README.md)
   - [Message](./types/message.md)
   - [Role](./types/role.md)
   - [MessageType](./types/message-type.md)

6. [Testing](./testing.md)
   - Unit Tests
   - Integration Tests
   - Test Utilities

7. [Security](./security.md)
   - Authentication
   - Token Management
   - Best Practices

8. [Contributing](./contributing.md)
   - Development Setup
   - Coding Standards
   - Pull Request Process

## Quick Start

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

## Support

If you encounter any issues or have questions, please:

1. Check the [FAQ](./faq.md)
2. Search [existing issues](https://github.com/ronydebnath/mcp-sdk/issues)
3. Create a new issue if needed

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). 