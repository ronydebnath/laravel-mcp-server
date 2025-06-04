<?php

namespace Ronydebnath\MCP\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Ronydebnath\MCP\Exceptions\ConnectionException;
use Ronydebnath\MCP\Types\Message;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\Chunk\ServerSentEvent;
use Symfony\Component\HttpClient\EventSourceHttpClient;

class StreamingMCPClient
{
    private EventSourceHttpClient $httpClient;
    private string $baseUrl;
    private ?string $sessionId = null;
    private EventDispatcher $dispatcher;

    public function __construct(array $config)
    {
        $this->baseUrl = sprintf(
            'http://%s:%d',
            $config['host'] ?? 'localhost',
            $config['port'] ?? 8000
        );

        $this->dispatcher = new EventDispatcher();
        $this->httpClient = new EventSourceHttpClient([
            'base_uri' => $this->baseUrl,
            'timeout' => $config['timeout'] ?? 30,
            'connect_timeout' => $config['timeout'] ?? 30,
        ]);
    }

    /**
     * Send a message and stream the response
     *
     * @param Message $message
     * @param callable $onMessage Callback for each message chunk
     * @param callable|null $onError Callback for errors
     * @return void
     * @throws ConnectionException
     */
    public function stream(Message $message, callable $onMessage, ?callable $onError = null): void
    {
        try {
            $headers = [
                'Accept' => 'text/event-stream',
                'Content-Type' => 'application/json',
            ];

            if ($this->sessionId) {
                $headers['MCP-Session-ID'] = $this->sessionId;
            }

            $response = $this->httpClient->request('POST', '/messages/stream', [
                'headers' => $headers,
                'json' => $message->toArray(),
            ]);

            foreach ($this->httpClient->stream($response) as $chunk) {
                if ($chunk instanceof ServerSentEvent) {
                    if ($chunk->getEvent() === 'message') {
                        $data = json_decode($chunk->getData(), true);
                        $onMessage($data);
                    }
                }
            }
        } catch (GuzzleException $e) {
            if ($onError) {
                $onError($e);
            }
            throw new ConnectionException(
                "Failed to stream message to MCP server: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Send multiple messages and stream the response
     *
     * @param array<Message> $messages
     * @param callable $onMessage Callback for each message chunk
     * @param callable|null $onError Callback for errors
     * @return void
     * @throws ConnectionException
     */
    public function streamMultiple(array $messages, callable $onMessage, ?callable $onError = null): void
    {
        try {
            $headers = [
                'Accept' => 'text/event-stream',
                'Content-Type' => 'application/json',
            ];

            if ($this->sessionId) {
                $headers['MCP-Session-ID'] = $this->sessionId;
            }

            $response = $this->httpClient->request('POST', '/messages/batch/stream', [
                'headers' => $headers,
                'json' => array_map(fn (Message $message) => $message->toArray(), $messages),
            ]);

            foreach ($this->httpClient->stream($response) as $chunk) {
                if ($chunk instanceof ServerSentEvent) {
                    if ($chunk->getEvent() === 'message') {
                        $data = json_decode($chunk->getData(), true);
                        $onMessage($data);
                    }
                }
            }
        } catch (GuzzleException $e) {
            if ($onError) {
                $onError($e);
            }
            throw new ConnectionException(
                "Failed to stream messages to MCP server: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Get the current session ID
     *
     * @return string|null
     */
    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * Set the session ID
     *
     * @param string $sessionId
     * @return void
     */
    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }
} 