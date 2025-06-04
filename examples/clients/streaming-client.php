<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Ronydebnath\MCP\Client\StreamingMCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

// Create a new streaming client instance
$client = new StreamingMCPClient();

// Set up message handler
$client->onMessage(function (Message $message) {
    echo "Received message:\n";
    echo "Role: " . $message->role . "\n";
    echo "Content: " . $message->content . "\n";
    echo "Type: " . $message->type . "\n";
    echo "-------------------\n";
});

// Set up error handler
$client->onError(function (\Throwable $error) {
    echo "Error: " . $error->getMessage() . "\n";
});

try {
    // Connect to the server
    $client->connect();
    
    // Create and send a message
    $message = new Message(
        role: Role::USER,
        content: 'Hello, how are you?',
        type: MessageType::TEXT
    );
    
    $client->send($message);
    
    // Keep the connection open for a while to receive messages
    sleep(5);
    
    // Close the connection
    $client->close();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 