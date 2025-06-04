<?php

namespace Ronydebnath\MCP\Server;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Ronydebnath\MCP\Server\Auth\AuthProvider;
use Ronydebnath\MCP\Server\Auth\AuthenticationException;

class MCPServer
{
    private array $sessions = [];
    private array $handlers = [];
    private array $middleware = [];
    protected ?AuthProvider $authProvider = null;

    public function __construct(
        private readonly array $config = [],
        ?AuthProvider $authProvider = null
    ) {
        $this->authProvider = $authProvider;
    }

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
        try {
            // Run middleware
            foreach ($this->middleware as $middleware) {
                $response = $middleware($request);
                if ($response instanceof Response) {
                    return $response;
                }
            }

            // Authenticate if provider is set
            if ($this->authProvider) {
                $token = $request->header('Authorization');
                if (!$token || !$this->authProvider->validateToken($token)) {
                    throw new AuthenticationException('Invalid or missing token');
                }
            }

            $data = $request->json()->all();
            $type = $data['type'] ?? 'text';
            $content = $data['content'] ?? '';
            $role = $data['role'] ?? 'user';

            $message = new Message(
                content: $content,
                role: Role::from($role),
                type: MessageType::from($type)
            );

            if (isset($this->handlers[$type])) {
                $response = $this->handlers[$type]($message);
                return new Response(
                    json_encode($response),
                    200,
                    ['Content-Type' => 'application/json']
                );
            }

            return new Response(
                json_encode(['error' => 'No handler found for message type']),
                404,
                ['Content-Type' => 'application/json']
            );
        } catch (AuthenticationException $e) {
            return new Response(
                json_encode(['error' => $e->getMessage()]),
                401,
                ['Content-Type' => 'application/json']
            );
        } catch (\Exception $e) {
            return new Response(
                json_encode(['error' => $e->getMessage()]),
                500,
                ['Content-Type' => 'application/json']
            );
        }
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
                'type' => MessageType::TEXT,
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
            type: $data['type'] ?? MessageType::TEXT
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
                'type' => MessageType::TEXT,
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
                'type' => MessageType::TEXT,
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

    public function on(string $type, callable $handler): void
    {
        $this->handlers[$type] = $handler;
    }

    public function use(callable $middleware): void
    {
        $this->middleware[] = $middleware;
    }
} 