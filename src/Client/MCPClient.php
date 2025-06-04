<?php

namespace Ronydebnath\MCP\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Ronydebnath\MCP\Exceptions\ConnectionException;
use Ronydebnath\MCP\Types\Message;

class MCPClient
{
    private Client $httpClient;
    private string $baseUrl;

    public function __construct(array $config)
    {
        $this->baseUrl = sprintf(
            'http://%s:%d',
            $config['host'] ?? 'localhost',
            $config['port'] ?? 8000
        );

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $config['timeout'] ?? 30,
            'connect_timeout' => $config['timeout'] ?? 30,
        ]);
    }

    /**
     * Send a single message to the MCP server
     *
     * @param Message $message
     * @return array
     * @throws ConnectionException
     */
    public function send(Message $message): array
    {
        try {
            $response = $this->httpClient->post('/messages', [
                'json' => $message->toArray(),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new ConnectionException(
                "Failed to send message to MCP server: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Send multiple messages to the MCP server
     *
     * @param array<Message> $messages
     * @return array
     * @throws ConnectionException
     */
    public function sendMultiple(array $messages): array
    {
        try {
            $response = $this->httpClient->post('/messages/batch', [
                'json' => array_map(fn (Message $message) => $message->toArray(), $messages),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new ConnectionException(
                "Failed to send messages to MCP server: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }
} 