<?php

namespace Ronydebnath\MCP\Shared;

use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;

class Memory
{
    private array $messages = [];
    private int $maxSize;

    public function __construct(int $maxSize = 100)
    {
        $this->maxSize = $maxSize;
    }

    /**
     * Add a message to memory
     *
     * @param Message $message
     * @return void
     */
    public function add(Message $message): void
    {
        $this->messages[] = $message;
        $this->trim();
    }

    /**
     * Add multiple messages to memory
     *
     * @param array $messages
     * @return void
     */
    public function addMultiple(array $messages): void
    {
        foreach ($messages as $message) {
            $this->add($message);
        }
    }

    /**
     * Get all messages from memory
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->messages;
    }

    /**
     * Get messages by role
     *
     * @param Role $role
     * @return array
     */
    public function getByRole(Role $role): array
    {
        return array_filter($this->messages, fn($message) => $message->role === $role);
    }

    /**
     * Get messages by type
     *
     * @param string|\Ronydebnath\MCP\Types\MessageType $type
     * @return array
     */
    public function getByType($type): array
    {
        if ($type instanceof \Ronydebnath\MCP\Types\MessageType) {
            $type = $type->value;
        }
        return array_filter($this->messages, fn($message) => $message->type->value === $type);
    }

    /**
     * Clear all messages from memory
     *
     * @return void
     */
    public function clear(): void
    {
        $this->messages = [];
    }

    /**
     * Get the current size of memory
     *
     * @return int
     */
    public function size(): int
    {
        return count($this->messages);
    }

    /**
     * Check if memory is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->messages);
    }

    /**
     * Check if memory is full
     *
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->size() >= $this->maxSize;
    }

    /**
     * Trim memory to max size if needed
     *
     * @return void
     */
    private function trim(): void
    {
        if ($this->size() > $this->maxSize) {
            $this->messages = array_slice($this->messages, -$this->maxSize);
        }
    }
} 