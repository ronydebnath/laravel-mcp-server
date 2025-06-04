<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Ronydebnath\MCP\Server\MCPServer;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

// Create a new server instance
$server = new MCPServer();

// Set up request handler
$server->onRequest(function (Message $message) {
    // Echo the received message
    echo "Received message:\n";
    echo "Role: " . $message->role . "\n";
    echo "Content: " . $message->content . "\n";
    echo "Type: " . $message->type . "\n";
    echo "-------------------\n";
    
    // Create and return a response
    return new Message(
        role: Role::ASSISTANT,
        content: "I received your message: " . $message->content,
        type: MessageType::TEXT
    );
});

// Start the server
echo "Starting MCP server on localhost:8000...\n";
$server->start('localhost', 8000); 