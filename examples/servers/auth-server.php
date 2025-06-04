<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Ronydebnath\MCP\Server\MCPServer;
use Ronydebnath\MCP\Server\Auth\AuthProvider;
use Ronydebnath\MCP\Server\Auth\AuthMiddleware;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

class AuthMCPServer extends MCPServer
{
    private AuthProvider $auth;
    
    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthProvider();
    }
    
    protected function handleMessage(Message $message): Message
    {
        // Check if message contains authentication token
        if (!isset($message->metadata['token'])) {
            return new Message(
                role: Role::ASSISTANT,
                content: "Authentication required. Please provide a valid token.",
                type: MessageType::TEXT
            );
        }
        
        // Validate token
        $token = $message->metadata['token'];
        if (!$this->auth->validateToken($token)) {
            return new Message(
                role: Role::ASSISTANT,
                content: "Invalid or expired token. Please authenticate again.",
                type: MessageType::TEXT
            );
        }
        
        // Process authenticated message
        return new Message(
            role: Role::ASSISTANT,
            content: "Authenticated message received: " . $message->content,
            type: MessageType::TEXT
        );
    }
    
    public function generateAuthToken(): string
    {
        return $this->auth->generateToken();
    }
}

// Create a new auth server instance
$server = new AuthMCPServer();

// Generate a test token
$token = $server->generateAuthToken();
echo "Generated test token: " . $token . "\n";

// Start the server
echo "Starting authenticated MCP server on localhost:8000...\n";
$server->start('localhost', 8000); 