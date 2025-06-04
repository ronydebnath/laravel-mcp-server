<?php

namespace Ronydebnath\MCP\Client;

use GuzzleHttp\Client;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Exceptions\ConnectionException;

class MCPClient
{
    protected Client $httpClient;
    protected string $baseUrl;

    public function __construct(string $baseUrl, ?Client $httpClient = null)
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = $httpClient ?? new Client();
    }

    public function sendMessage(Message $message): array
    {
        try {
            $response = $this->httpClient->post($this->baseUrl . '/messages', [
                'json' => [
                    'role' => $message->role->value,
                    'content' => $message->content,
                    'type' => $message->type->value
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new ConnectionException('Failed to send message to MCP server: ' . $e->getMessage(), 0, $e);
        }
    }

    public function sendMessages(array $messages): array
    {
        try {
            $response = $this->httpClient->post($this->baseUrl . '/messages/batch', [
                'json' => array_map(function (Message $message) {
                    return [
                        'role' => $message->role->value,
                        'content' => $message->content,
                        'type' => $message->type->value
                    ];
                }, $messages)
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new ConnectionException('Failed to send messages to MCP server: ' . $e->getMessage(), 0, $e);
        }
    }
} 