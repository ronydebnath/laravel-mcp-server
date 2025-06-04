<?php

namespace Ronydebnath\MCP\Tests\Server;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Ronydebnath\MCP\Server\MCPServer;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

class MCPServerTest extends TestCase
{
    private MCPServer $server;

    protected function setUp(): void
    {
        parent::setUp();
        $this->server = new MCPServer();
    }

    public function test_can_handle_regular_request(): void
    {
        $request = $this->createRequest([
            'role' => Role::USER->value,
            'content' => 'Hello',
            'type' => 'text',
        ]);

        $this->server->registerHandler('text', function (Message $message) {
            return [
                'role' => Role::ASSISTANT->value,
                'content' => 'Hi there!',
                'type' => 'text',
            ];
        });

        $response = $this->server->handle($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(Role::ASSISTANT->value, $data['role']);
        $this->assertEquals('Hi there!', $data['content']);
        $this->assertEquals('text', $data['type']);
    }

    public function test_can_handle_streaming_request(): void
    {
        $request = $this->createRequest([
            'role' => Role::USER->value,
            'content' => 'Hello',
            'type' => 'text',
        ], true);

        $this->server->registerHandler('text', function (Message $message) {
            return [
                'role' => Role::ASSISTANT->value,
                'content' => 'Hi there!',
                'type' => 'text',
            ];
        });

        $response = $this->server->handle($request);

        $this->assertEquals('text/event-stream', $response->headers->get('Content-Type'));
        $this->assertNotNull($response->headers->get('MCP-Session-ID'));
    }

    public function test_handles_missing_handler(): void
    {
        $request = $this->createRequest([
            'role' => Role::USER->value,
            'content' => 'Hello',
            'type' => 'unknown',
        ]);

        $response = $this->server->handle($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(Role::ASSISTANT->value, $data['role']);
        $this->assertStringContainsString('No handler registered', $data['content']);
    }

    public function test_middleware_can_intercept_request(): void
    {
        $request = $this->createRequest([
            'role' => Role::USER->value,
            'content' => 'Hello',
            'type' => 'text',
        ]);

        $this->server->registerMiddleware(function ($request) {
            return response()->json([
                'role' => Role::ASSISTANT->value,
                'content' => 'Intercepted!',
                'type' => 'text',
            ]);
        });

        $response = $this->server->handle($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(Role::ASSISTANT->value, $data['role']);
        $this->assertEquals('Intercepted!', $data['content']);
    }

    private function createRequest(array $data, bool $streaming = false): Request
    {
        $request = Request::create('/mcp', 'POST', [], [], [], [], json_encode($data));
        
        if ($streaming) {
            $request->headers->set('Accept', 'text/event-stream');
        }

        return $request;
    }
} 