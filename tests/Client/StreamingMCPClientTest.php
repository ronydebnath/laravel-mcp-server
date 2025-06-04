<?php

namespace Ronydebnath\MCP\Tests\Client;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Ronydebnath\MCP\Client\StreamingMCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

class StreamingMCPClientTest extends TestCase
{
    protected StreamingMCPClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $mockResponse = new MockResponse(
            "{\"role\":\"assistant\",\"content\":\"Hi!\",\"type\":\"text\"}\n",
            ['response_headers' => ['MCP-Session-ID: test-session']]
        );
        $mockClient = new MockHttpClient($mockResponse);
        $this->client = new StreamingMCPClient('http://localhost:8000', $mockClient);
    }

    public function test_can_stream_message()
    {
        $message = new Message(
            content: 'Hello',
            role: Role::USER,
            type: MessageType::TEXT
        );

        $receivedMessages = [];
        $this->client->onMessage(function ($message) use (&$receivedMessages) {
            $receivedMessages[] = $message;
        });

        $this->client->streamMessage($message);
        $this->assertNotEmpty($receivedMessages);
        $this->assertEquals('assistant', $receivedMessages[0]['role']);
        $this->assertEquals('Hi!', $receivedMessages[0]['content']);
        $this->assertEquals('text', $receivedMessages[0]['type']);
    }

    public function test_can_stream_multiple_messages()
    {
        $mockResponse = new MockResponse(
            "{\"role\":\"assistant\",\"content\":\"Hi!\",\"type\":\"text\"}\n{\"role\":\"assistant\",\"content\":\"How can I help?\",\"type\":\"text\"}\n",
            ['response_headers' => ['MCP-Session-ID: test-session']]
        );
        $mockClient = new MockHttpClient($mockResponse);
        $client = new StreamingMCPClient('http://localhost:8000', $mockClient);

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

        $receivedMessages = [];
        $client->onMessage(function ($message) use (&$receivedMessages) {
            $receivedMessages[] = $message;
        });

        $client->streamMessages($messages);
        $this->assertNotEmpty($receivedMessages);
        $this->assertEquals('assistant', $receivedMessages[0]['role']);
        $this->assertEquals('Hi!', $receivedMessages[0]['content']);
        $this->assertEquals('text', $receivedMessages[0]['type']);
        $this->assertEquals('assistant', $receivedMessages[1]['role']);
        $this->assertEquals('How can I help?', $receivedMessages[1]['content']);
        $this->assertEquals('text', $receivedMessages[1]['type']);
    }

    public function test_handles_streaming_error()
    {
        $mockClient = new MockHttpClient(function () {
            throw new \Exception('Simulated error');
        });
        $client = new StreamingMCPClient('http://localhost:8000', $mockClient);

        $message = new Message(
            content: 'Hello',
            role: Role::USER,
            type: MessageType::TEXT
        );

        $error = null;
        $client->onError(function ($e) use (&$error) {
            $error = $e;
        });

        $client->streamMessage($message);
        $this->assertNotNull($error);
        $this->assertEquals('Simulated error', $error->getMessage());
    }

    public function test_session_id_management()
    {
        $this->assertNull($this->client->getSessionId());
        $message = new Message(
            content: 'Hello',
            role: Role::USER,
            type: MessageType::TEXT
        );
        $this->client->streamMessage($message);
        $this->assertEquals('test-session', $this->client->getSessionId());
    }
} 