<?php

namespace Ronydebnath\MCP\Shared;

use Ronydebnath\MCP\Types\Message;

class Context
{
    private array $data = [];
    private Memory $memory;

    public function __construct(?Memory $memory = null)
    {
        $this->memory = $memory ?? new Memory();
    }

    /**
     * Set a context value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Get a context value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Check if a context key exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Remove a context value
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        unset($this->data[$key]);
    }

    /**
     * Clear all context data
     *
     * @return void
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * Get all context data
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->data;
    }

    /**
     * Get the memory instance
     *
     * @return Memory
     */
    public function getMemory(): Memory
    {
        return $this->memory;
    }

    /**
     * Set the memory instance
     *
     * @param Memory $memory
     * @return void
     */
    public function setMemory(Memory $memory): void
    {
        $this->memory = $memory;
    }

    /**
     * Add a message to memory
     *
     * @param Message $message
     * @return void
     */
    public function addMessage(Message $message): void
    {
        $this->memory->add($message);
    }

    /**
     * Get all messages from memory
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->memory->getAll();
    }

    /**
     * Clear all messages from memory
     *
     * @return void
     */
    public function clearMessages(): void
    {
        $this->memory->clear();
    }

    /**
     * Get the context as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'messages' => $this->memory->getAll(),
        ];
    }
} 