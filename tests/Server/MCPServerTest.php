<?php

namespace Ronydebnath\MCP\Tests\Server;

use PHPUnit\Framework\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ronydebnath\MCP\Server\MCPServer;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

class MCPServerTest extends TestCase
{
    protected MCPServer $server;

    protected function setUp(): void
    {
        parent::setUp();
        $this->server = new MCPServer();
    }

    public function test_can_handle_regular_request()
    {
        $request = Request::create('/messages', 'POST', [], [], [], [], json_encode([
            'content' => 'Hello',
            'role' => 'user',
            'type' => 'text'
        ]));

        $this->server->on('text', function (Message $message) {
            return ['response' => 'Received: ' . $message->content];
        });

        $response = $this->server->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['response' => 'Received: Hello'], json_decode($response->getContent(), true));
    }

    public function test_handles_missing_handler()
    {
        $request = Request::create('/messages', 'POST', [], [], [], [], json_encode([
            'content' => 'Hello',
            'role' => 'user',
            'type' => 'unknown'
        ]));

        $response = $this->server->handle($request);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(['error' => 'No handler found for message type'], json_decode($response->getContent(), true));
    }

    public function test_middleware_can_intercept_request()
    {
        $request = Request::create('/messages', 'POST', [], [], [], [], json_encode([
            'content' => 'Hello',
            'role' => 'user',
            'type' => 'text'
        ]));

        $this->server->use(function ($request) {
            return response()->json(['error' => 'Middleware intercepted'], 403);
        });

        $response = $this->server->handle($request);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['error' => 'Middleware intercepted'], json_decode($response->getContent(), true));
    }
} 