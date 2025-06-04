<?php

namespace Ronydebnath\MCP\Tests\Client;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Ronydebnath\MCP\Client\MCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

class MCPClientTest extends TestCase
{
    protected MCPClient $client;
    protected MockHandler $mock;
    protected Client $httpClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mock = new MockHandler();
        $handlerStack = HandlerStack::create($this->mock);
        $this->httpClient = new Client(['handler' => $handlerStack]);
        $this->client = new MCPClient('http://localhost:8000', $this->httpClient);
    }

    public function test_can_send_message()
    {
        $this->mock->append(new Response(200, [], json_encode([
            'role' => 'assistant',
            'content' => 'Hi there!',
            'type' => 'text'
        ])));

        $message = new Message(
            content: 'Hello',
            role: Role::USER,
            type: MessageType::TEXT
        );

        $response = $this->client->sendMessage($message);
        $this->assertEquals('assistant', $response['role']);
        $this->assertEquals('Hi there!', $response['content']);
        $this->assertEquals('text', $response['type']);
    }

    public function test_can_send_multiple_messages()
    {
        $this->mock->append(new Response(200, [], json_encode([
            'role' => 'assistant',
            'content' => 'Hi there!',
            'type' => 'text'
        ])));

        $messages = [
            new Message(
                content: 'Hello',
                role: Role::USER,
                type: MessageType::TEXT
            ),
            new Message(
                content: 'How are you?',
                role: Role::USER,
                type: MessageType::TEXT
            )
        ];

        $response = $this->client->sendMessages($messages);
        $this->assertEquals('assistant', $response['role']);
        $this->assertEquals('Hi there!', $response['content']);
        $this->assertEquals('text', $response['type']);
    }

    public function test_handles_connection_error()
    {
        $this->expectException(\Ronydebnath\MCP\Exceptions\ConnectionException::class);

        $client = new MCPClient([
            'host' => 'invalid-host',
            'port' => 9999,
            'timeout' => 1,
        ]);

        $message = new Message(
            role: Role::USER,
            content: 'Hello, MCP!',
            type: MessageType::TEXT
        );

        $client->send($message);
    }
} 