<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Ronydebnath\MCP\Server\MCPServer;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

class CustomMCPServer extends MCPServer
{
    private array $messageHistory = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->setSessionExpiry(3600); // 1 hour
        $this->setMaxConnections(100);
    }
    
    protected function handleMessage(Message $message): Message
    {
        // Store message in history
        $this->messageHistory[] = $message;
        
        // Limit history size
        if (count($this->messageHistory) > 100) {
            array_shift($this->messageHistory);
        }
        
        // Create response based on message history
        $context = $this->getMessageContext();
        
        return new Message(
            role: Role::ASSISTANT,
            content: "I understand your message in context: " . $context . "\nYour message: " . $message->content,
            type: MessageType::TEXT
        );
    }
    
    private function getMessageContext(): string
    {
        if (empty($this->messageHistory)) {
            return "No previous context";
        }
        
        $lastMessages = array_slice($this->messageHistory, -3);
        $context = [];
        
        foreach ($lastMessages as $msg) {
            $context[] = $msg->role . ": " . $msg->content;
        }
        
        return implode("\n", $context);
    }
}

// Create a new custom server instance
$server = new CustomMCPServer();

// Start the server
echo "Starting custom MCP server on localhost:8000...\n";
$server->start('localhost', 8000); 