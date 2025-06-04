<?php

namespace Ronydebnath\MCP\Server;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MCPServer
{
    private array $sessions = [];
    private array $handlers = [];
    private array $middleware = [];

    public function __construct(
        private readonly array $config = []
    ) {}

    /**
     * Register a message handler
     *
     * @param string $type Message type to handle
     * @param callable $handler Handler function
     * @return void
     */
    public function registerHandler(string $type, callable $handler): void
    {
        $this->handlers[$type] = $handler;
    }

    /**
     * Register middleware
     *
     * @param callable $middleware Middleware function
     * @return void
     */
    public function registerMiddleware(callable $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * Handle an incoming request
     *
     * @param Request $request
     * @return Response|StreamedResponse
     */
    public function handle(Request $request): Response|StreamedResponse
    {
        // Apply middleware
        foreach ($this->middleware as $middleware) {
            $response = $middleware($request);
            if ($response instanceof Response) {
                return $response;
            }
        }

        // Check if this is a streaming request
        if ($request->header('Accept') === 'text/event-stream') {
            return $this->handleStreamingRequest($request);
        }

        // Handle regular request
        $message = $this->parseMessage($request);
        $response = $this->processMessage($message);
        
        return response()->json($response);
    }

    /**
     * Handle a streaming request
     *
     * @param Request $request
     * @return StreamedResponse
     */
    private function handleStreamingRequest(Request $request): StreamedResponse
    {
        $sessionId = $request->header('MCP-Session-ID') ?? Str::uuid()->toString();
        $this->sessions[$sessionId] = [
            'last_event_id' => $request->header('Last-Event-ID'),
            'created_at' => now(),
        ];

        return new StreamedResponse(function () use ($request, $sessionId) {
            $message = $this->parseMessage($request);
            
            // Send initial response
            $this->sendSSE('message', [
                'role' => Role::ASSISTANT->value,
                'content' => 'Connected to MCP server',
                'type' => 'text',
            ]);

            // Process message and stream response
            $this->streamResponse($message, $sessionId);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'MCP-Session-ID' => $sessionId,
        ]);
    }

    /**
     * Parse message from request
     *
     * @param Request $request
     * @return Message
     */
    private function parseMessage(Request $request): Message
    {
        $data = $request->json()->all();
        
        return new Message(
            role: Role::from($data['role']),
            content: $data['content'],
            type: $data['type'] ?? 'text'
        );
    }

    /**
     * Process a message and return response
     *
     * @param Message $message
     * @return array
     */
    private function processMessage(Message $message): array
    {
        $handler = $this->handlers[$message->type] ?? null;
        
        if (!$handler) {
            return [
                'role' => Role::ASSISTANT->value,
                'content' => 'No handler registered for message type: ' . $message->type,
                'type' => 'text',
            ];
        }

        $response = $handler($message);
        
        if ($response instanceof Message) {
            return $response->toArray();
        }

        return $response;
    }

    /**
     * Stream response chunks
     *
     * @param Message $message
     * @param string $sessionId
     * @return void
     */
    private function streamResponse(Message $message, string $sessionId): void
    {
        $handler = $this->handlers[$message->type] ?? null;
        
        if (!$handler) {
            $this->sendSSE('message', [
                'role' => Role::ASSISTANT->value,
                'content' => 'No handler registered for message type: ' . $message->type,
                'type' => 'text',
            ]);
            return;
        }

        $response = $handler($message);
        
        if ($response instanceof Message) {
            $this->sendSSE('message', $response->toArray());
        } else {
            $this->sendSSE('message', $response);
        }
    }

    /**
     * Send SSE event
     *
     * @param string $event
     * @param array $data
     * @return void
     */
    private function sendSSE(string $event, array $data): void
    {
        echo "event: {$event}\n";
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();
    }

    /**
     * Get session information
     *
     * @param string $sessionId
     * @return array|null
     */
    public function getSession(string $sessionId): ?array
    {
        return $this->sessions[$sessionId] ?? null;
    }

    /**
     * Clean up expired sessions
     *
     * @return void
     */
    public function cleanupSessions(): void
    {
        $expiry = now()->subMinutes($this->config['session_expiry'] ?? 60);
        
        foreach ($this->sessions as $sessionId => $session) {
            if ($session['created_at'] < $expiry) {
                unset($this->sessions[$sessionId]);
            }
        }
    }
} 