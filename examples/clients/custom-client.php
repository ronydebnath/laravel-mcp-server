<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Ronydebnath\MCP\Client\MCPClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

class CustomMCPClient extends MCPClient
{
    public function __construct()
    {
        parent::__construct([
            'host' => 'localhost',
            'port' => 8000,
            'timeout' => 30,
            'verify' => true,
        ]);
    }

    public function sendWithRetry(Message $message, int $maxRetries = 3): Message
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts < $maxRetries) {
            try {
                return $this->send($message);
            } catch (\Exception $e) {
                $lastException = $e;
                $attempts++;
                
                if ($attempts < $maxRetries) {
                    // Wait before retrying (exponential backoff)
                    sleep(pow(2, $attempts));
                }
            }
        }

        throw $lastException;
    }
}

// Create a new custom client instance
$client = new CustomMCPClient();

// Create a message
$message = new Message(
    role: Role::USER,
    content: 'Hello, how are you?',
    type: MessageType::TEXT
);

try {
    // Send the message with retry logic
    $response = $client->sendWithRetry($message);
    
    // Print the response
    echo "Response from server:\n";
    echo "Role: " . $response->role . "\n";
    echo "Content: " . $response->content . "\n";
    echo "Type: " . $response->type . "\n";
} catch (\Exception $e) {
    echo "Error after all retries: " . $e->getMessage() . "\n";
} 