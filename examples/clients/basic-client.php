<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Ronydebnath\MCP\Client\MCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

// Create a new client instance
$client = new MCPClient();

// Create a message
$message = new Message(
    role: Role::USER,
    content: 'Hello, how are you?',
    type: 'text'
);

try {
    // Send the message and get the response
    $response = $client->send($message);
    
    // Print the response
    echo "Response from server:\n";
    echo "Role: " . $response->role . "\n";
    echo "Content: " . $response->content . "\n";
    echo "Type: " . $response->type . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 