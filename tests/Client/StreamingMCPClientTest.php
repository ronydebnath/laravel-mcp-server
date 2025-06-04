<?php

namespace Ronydebnath\MCP\Tests\Client;

use PHPUnit\Framework\TestCase;
use Ronydebnath\MCP\Client\StreamingMCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

class StreamingMCPClientTest extends TestCase
{
    private StreamingMCPClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new StreamingMCPClient([
            'host' => 'localhost',
            'port' => 8000,
            'timeout' => 30,
        ]);
    }

    public function test_can_stream_message()
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello, MCP!',
            type: MessageType::TEXT
        );

        $receivedChunks = [];
        $onMessage = function ($chunk) use (&$receivedChunks) {
            $receivedChunks[] = $chunk;
        };

        $this->client->stream($message, $onMessage);

        $this->assertNotEmpty($receivedChunks);
        $this->assertArrayHasKey('role', $receivedChunks[0]);
        $this->assertArrayHasKey('content', $receivedChunks[0]);
        $this->assertArrayHasKey('type', $receivedChunks[0]);
    }

    public function test_can_stream_multiple_messages()
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

        $receivedChunks = [];
        $onMessage = function ($chunk) use (&$receivedChunks) {
            $receivedChunks[] = $chunk;
        };

        $this->client->streamMultiple($messages, $onMessage);

        $this->assertNotEmpty($receivedChunks);
        $this->assertArrayHasKey('role', $receivedChunks[0]);
        $this->assertArrayHasKey('content', $receivedChunks[0]);
        $this->assertArrayHasKey('type', $receivedChunks[0]);
    }

    public function test_handles_streaming_error()
    {
        $this->expectException(\Ronydebnath\MCP\Exceptions\ConnectionException::class);

        $client = new StreamingMCPClient([
            'host' => 'invalid-host',
            'port' => 9999,
            'timeout' => 1,
        ]);

        $message = new Message(
            role: Role::USER,
            content: 'Hello, MCP!',
            type: MessageType::TEXT
        );

        $errorCaught = false;
        $onError = function ($error) use (&$errorCaught) {
            $errorCaught = true;
        };

        $client->stream($message, function () {}, $onError);
        $this->assertTrue($errorCaught);
    }

    public function test_session_id_management()
    {
        $sessionId = 'test-session-123';
        $this->client->setSessionId($sessionId);
        $this->assertEquals($sessionId, $this->client->getSessionId());
    }
} 