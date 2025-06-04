<?php

namespace Ronydebnath\MCP\Tests\Client;

use PHPUnit\Framework\TestCase;
use Ronydebnath\MCP\Client\MCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

class MCPClientTest extends TestCase
{
    private MCPClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new MCPClient([
            'host' => 'localhost',
            'port' => 8000,
            'timeout' => 30,
        ]);
    }

    public function test_can_send_message()
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello, MCP!',
            type: MessageType::TEXT
        );

        $response = $this->client->send($message);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('role', $response);
        $this->assertArrayHasKey('content', $response);
        $this->assertArrayHasKey('type', $response);
        $this->assertEquals('assistant', $response['role']);
    }

    public function test_can_send_multiple_messages()
    {
        $messages = [
            new Message(
                role: Role::USER,
                content: 'Hello, MCP!',
                type: MessageType::TEXT
            ),
            new Message(
                role: Role::ASSISTANT,
                content: 'Hi there!',
                type: MessageType::TEXT
            ),
        ];

        $response = $this->client->sendMultiple($messages);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('role', $response);
        $this->assertArrayHasKey('content', $response);
        $this->assertArrayHasKey('type', $response);
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