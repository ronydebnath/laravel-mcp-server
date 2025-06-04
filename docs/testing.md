# Testing

This document outlines testing practices and examples for the MCP SDK.

## Table of Contents

1. [Test Structure](#test-structure)
2. [Client Tests](#client-tests)
3. [Server Tests](#server-tests)
4. [Shared Component Tests](#shared-component-tests)
5. [Best Practices](#best-practices)

## Test Structure

The MCP SDK uses PHPUnit for testing. Tests are organized in the `tests` directory:

```
tests/
├── Client/
│   ├── MCPClientTest.php
│   └── StreamingMCPClientTest.php
├── Server/
│   ├── MCPServerTest.php
│   └── Auth/
│       ├── AuthProviderTest.php
│       └── AuthMiddlewareTest.php
└── Shared/
    ├── MemoryTest.php
    ├── ProgressTest.php
    └── ContextTest.php
```

## Client Tests

### MCPClientTest

```php
use Ronydebnath\MCP\Client\MCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

class MCPClientTest extends TestCase
{
    private MCPClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = app(MCPClient::class);
    }

    public function test_can_send_message()
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello',
            type: 'text'
        );

        $response = $this->client->send($message);

        $this->assertInstanceOf(Message::class, $response);
        $this->assertEquals(Role::ASSISTANT, $response->role);
    }

    public function test_handles_connection_error()
    {
        $this->expectException(ConnectionException::class);

        $this->client->setConnection('invalid');
        $this->client->send(new Message(
            role: Role::USER,
            content: 'Hello',
            type: 'text'
        ));
    }
}
```

### StreamingMCPClientTest

```php
use Ronydebnath\MCP\Client\StreamingMCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

class StreamingMCPClientTest extends TestCase
{
    private StreamingMCPClient $client;
    private array $receivedMessages = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = app(StreamingMCPClient::class);
    }

    public function test_can_receive_messages()
    {
        $this->client->onMessage(function (Message $message) {
            $this->receivedMessages[] = $message;
        });

        $this->client->connect();
        $this->client->send(new Message(
            role: Role::USER,
            content: 'Hello',
            type: 'text'
        ));

        $this->assertNotEmpty($this->receivedMessages);
        $this->assertInstanceOf(Message::class, $this->receivedMessages[0]);
    }

    public function test_handles_connection_error()
    {
        $this->expectException(ConnectionException::class);

        $this->client->setConnection('invalid');
        $this->client->connect();
    }
}
```

## Server Tests

### MCPServerTest

```php
use Ronydebnath\MCP\Server\MCPServer;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

class MCPServerTest extends TestCase
{
    private MCPServer $server;

    protected function setUp(): void
    {
        parent::setUp();
        $this->server = app(MCPServer::class);
    }

    public function test_can_handle_request()
    {
        $request = $this->createRequest([
            'message' => [
                'role' => Role::USER,
                'content' => 'Hello',
                'type' => 'text',
            ],
        ]);

        $response = $this->server->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function test_validates_request_data()
    {
        $request = $this->createRequest([
            'message' => [
                'role' => 'invalid',
                'content' => '',
                'type' => 'invalid',
            ],
        ]);

        $response = $this->server->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    private function createRequest(array $data): Request
    {
        return Request::create('/mcp', 'POST', [], [], [], [], json_encode($data));
    }
}
```

### AuthProviderTest

```php
use Ronydebnath\MCP\Server\Auth\AuthProvider;

class AuthProviderTest extends TestCase
{
    private AuthProvider $auth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = app(AuthProvider::class);
    }

    public function test_can_generate_token()
    {
        $token = $this->auth->generateToken();

        $this->assertIsString($token);
        $this->assertEquals(32, strlen($token));
    }

    public function test_can_validate_token()
    {
        $token = $this->auth->generateToken();

        $this->assertTrue($this->auth->validateToken($token));
    }

    public function test_token_expires()
    {
        $token = $this->auth->generateToken();
        
        // Simulate time passing
        $this->travel(61)->seconds();

        $this->assertFalse($this->auth->validateToken($token));
    }
}
```

## Shared Component Tests

### MemoryTest

```php
use Ronydebnath\MCP\Shared\Memory;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

class MemoryTest extends TestCase
{
    private Memory $memory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memory = app(Memory::class);
    }

    public function test_can_add_message()
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello',
            type: 'text'
        );

        $this->memory->add($message);

        $this->assertCount(1, $this->memory->getMessages());
    }

    public function test_respects_max_size()
    {
        $this->memory->setMaxSize(2);

        $this->memory->add(new Message(
            role: Role::USER,
            content: 'Message 1',
            type: 'text'
        ));

        $this->memory->add(new Message(
            role: Role::USER,
            content: 'Message 2',
            type: 'text'
        ));

        $this->memory->add(new Message(
            role: Role::USER,
            content: 'Message 3',
            type: 'text'
        ));

        $this->assertCount(2, $this->memory->getMessages());
    }
}
```

### ProgressTest

```php
use Ronydebnath\MCP\Shared\Progress;

class ProgressTest extends TestCase
{
    private Progress $progress;

    protected function setUp(): void
    {
        parent::setUp();
        $this->progress = app(Progress::class);
    }

    public function test_can_update_progress()
    {
        $this->progress->update(50, 'Processing...', ['step' => 'analysis']);

        $this->assertEquals(50, $this->progress->getPercentage());
        $this->assertEquals('Processing...', $this->progress->getStatus());
        $this->assertEquals(['step' => 'analysis'], $this->progress->getMetadata());
    }

    public function test_can_increment_progress()
    {
        $this->progress->update(50);
        $this->progress->increment(10);

        $this->assertEquals(60, $this->progress->getPercentage());
    }
}
```

### ContextTest

```php
use Ronydebnath\MCP\Shared\Context;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

class ContextTest extends TestCase
{
    private Context $context;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = app(Context::class);
    }

    public function test_can_set_and_get_context_value()
    {
        $this->context->set('key', 'value');

        $this->assertEquals('value', $this->context->get('key'));
    }

    public function test_returns_default_value_when_key_not_found()
    {
        $this->assertEquals('default', $this->context->get('key', 'default'));
    }

    public function test_can_add_message_to_memory()
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello',
            type: 'text'
        );

        $this->context->addMessage($message);

        $this->assertCount(1, $this->context->getMessages());
    }
}
```

## Best Practices

### 1. Use Test Doubles

Use test doubles (mocks, stubs) to isolate components:

```php
use PHPUnit\Framework\MockObject\MockObject;

class YourTest extends TestCase
{
    private MockObject $memory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memory = $this->createMock(Memory::class);
    }

    public function test_something()
    {
        $this->memory->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(Message::class));
    }
}
```

### 2. Test Edge Cases

Always test edge cases and error conditions:

```php
public function test_handles_empty_message()
{
    $this->expectException(InvalidArgumentException::class);

    new Message(
        role: Role::USER,
        content: '',
        type: 'text'
    );
}
```

### 3. Use Data Providers

Use data providers for testing multiple scenarios:

```php
/**
 * @dataProvider messageProvider
 */
public function test_validates_message_type(string $type, bool $isValid)
{
    if (!$isValid) {
        $this->expectException(InvalidArgumentException::class);
    }

    new Message(
        role: Role::USER,
        content: 'Hello',
        type: $type
    );
}

public function messageProvider(): array
{
    return [
        'valid text' => ['text', true],
        'valid image' => ['image', true],
        'valid audio' => ['audio', true],
        'invalid type' => ['invalid', false],
    ];
}
```

### 4. Test Asynchronous Code

Use proper techniques for testing asynchronous code:

```php
public function test_handles_async_operation()
{
    $promise = $this->client->sendAsync($message);
    
    $this->assertInstanceOf(Promise::class, $promise);
    
    $response = $promise->wait();
    
    $this->assertInstanceOf(Message::class, $response);
}
```

### 5. Clean Up Resources

Always clean up resources in `tearDown`:

```php
protected function tearDown(): void
{
    $this->client->close();
    parent::tearDown();
}
```

## Next Steps

- Learn about [Client Components](./client/README.md)
- Explore [Server Components](./server/README.md)
- Understand [Shared Components](./shared/README.md)
- Read about [Security](./security.md) 