<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Ronydebnath\MCP\Shared\Context;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

// Create a new context instance
$context = new Context();

// Set some context values
$context->set('user_id', 123);
$context->set('preferences', [
    'theme' => 'dark',
    'language' => 'en',
    'notifications' => true
]);

// Add some messages to context
$context->addMessage(new Message(
    role: Role::USER,
    content: "Hello, how are you?",
    type: MessageType::TEXT
));

$context->addMessage(new Message(
    role: Role::ASSISTANT,
    content: "I'm doing well, thank you!",
    type: MessageType::TEXT
));

// Get and display context values
echo "User ID: " . $context->get('user_id') . "\n";
echo "Preferences: " . json_encode($context->get('preferences')) . "\n";
echo "Default value for non-existent key: " . $context->get('non_existent', 'default') . "\n";
echo "Has user_id: " . ($context->has('user_id') ? 'yes' : 'no') . "\n";
echo "Has non_existent: " . ($context->has('non_existent') ? 'yes' : 'no') . "\n";

// Display messages
echo "\nMessages in context:\n";
foreach ($context->getMessages() as $message) {
    echo $message->role . ": " . $message->content . "\n";
}

// Remove a value
$context->remove('preferences');
echo "\nAfter removing preferences:\n";
echo "Has preferences: " . ($context->has('preferences') ? 'yes' : 'no') . "\n";

// Clear messages
$context->clearMessages();
echo "\nAfter clearing messages:\n";
echo "Message count: " . count($context->getMessages()) . "\n";

// Clear all context
$context->clear();
echo "\nAfter clearing all context:\n";
echo "Has user_id: " . ($context->has('user_id') ? 'yes' : 'no') . "\n";
echo "Message count: " . count($context->getMessages()) . "\n"; 