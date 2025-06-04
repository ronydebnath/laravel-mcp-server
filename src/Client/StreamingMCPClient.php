<?php

namespace Ronydebnath\MCP\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\HttpClient\HttpClient;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Exceptions\ConnectionException;

class StreamingMCPClient
{
    protected HttpClientInterface $httpClient;
    protected string $baseUrl;
    protected ?string $sessionId = null;
    protected $onMessageCallback = null;
    protected $onErrorCallback = null;

    public function __construct(string $baseUrl, ?HttpClientInterface $httpClient = null)
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    public function onMessage(callable $callback): void
    {
        $this->onMessageCallback = $callback;
    }

    public function onError(callable $callback): void
    {
        $this->onErrorCallback = $callback;
    }

    public function streamMessage(Message $message): void
    {
        try {
            $response = $this->httpClient->request('POST', $this->baseUrl . '/messages/stream', [
                'json' => [
                    'role' => $message->role->value,
                    'content' => $message->content,
                    'type' => $message->type->value
                ],
                'headers' => [
                    'Accept' => 'text/event-stream'
                ]
            ]);

            $this->sessionId = $response->getHeaders()['MCP-Session-ID'][0] ?? null;

            foreach ($this->httpClient->stream($response) as $chunk) {
                if ($chunk->isTimeout() || $chunk->isFirst() || $chunk->isLast()) {
                    continue;
                }
                $data = $chunk->getContent();
                if ($this->onMessageCallback && $data) {
                    $decoded = json_decode($data, true);
                    if ($decoded) {
                        ($this->onMessageCallback)($decoded);
                    }
                }
            }
        } catch (\Exception $e) {
            if ($this->onErrorCallback) {
                ($this->onErrorCallback)($e);
            } else {
                throw new ConnectionException('Failed to stream message: ' . $e->getMessage(), 0, $e);
            }
        }
    }

    public function streamMessages(array $messages): void
    {
        try {
            $response = $this->httpClient->request('POST', $this->baseUrl . '/messages/stream/batch', [
                'json' => array_map(function (Message $message) {
                    return [
                        'role' => $message->role->value,
                        'content' => $message->content,
                        'type' => $message->type->value
                    ];
                }, $messages),
                'headers' => [
                    'Accept' => 'text/event-stream'
                ]
            ]);

            $this->sessionId = $response->getHeaders()['MCP-Session-ID'][0] ?? null;

            foreach ($this->httpClient->stream($response) as $chunk) {
                if ($chunk->isTimeout() || $chunk->isFirst() || $chunk->isLast()) {
                    continue;
                }
                $data = $chunk->getContent();
                if ($this->onMessageCallback && $data) {
                    $decoded = json_decode($data, true);
                    if ($decoded) {
                        ($this->onMessageCallback)($decoded);
                    }
                }
            }
        } catch (\Exception $e) {
            if ($this->onErrorCallback) {
                ($this->onErrorCallback)($e);
            } else {
                throw new ConnectionException('Failed to stream messages: ' . $e->getMessage(), 0, $e);
            }
        }
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }
} 