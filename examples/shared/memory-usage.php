<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Ronydebnath\MCP\Shared\Memory;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

// Create a new memory instance
$memory = new Memory();

// Add some messages
$memory->add(new Message(
    role: Role::USER,
    content: "Hello, how are you?",
    type: MessageType::TEXT
));

$memory->add(new Message(
    role: Role::ASSISTANT,
    content: "I'm doing well, thank you!",
    type: MessageType::TEXT
));

$memory->add(new Message(
    role: Role::USER,
    content: "What's the weather like?",
    type: MessageType::TEXT
));

// Get all messages
echo "All messages:\n";
foreach ($memory->getMessages() as $message) {
    echo $message->role . ": " . $message->content . "\n";
}
echo "-------------------\n";

// Get messages by role
echo "User messages:\n";
foreach ($memory->getMessagesByRole(Role::USER) as $message) {
    echo $message->content . "\n";
}
echo "-------------------\n";

// Get messages by type
echo "Text messages:\n";
foreach ($memory->getMessagesByType(MessageType::TEXT) as $message) {
    echo $message->role . ": " . $message->content . "\n";
}
echo "-------------------\n";

// Clear memory
$memory->clear();
echo "Memory cleared. Message count: " . count($memory->getMessages()) . "\n"; 