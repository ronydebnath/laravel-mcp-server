<?php

namespace Ronydebnath\MCP\Types;

class Message
{
    public function __construct(
        public readonly Role $role,
        public readonly string $content,
        public readonly MessageType $type = MessageType::TEXT
    ) {}

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'content' => $this->content,
            'type' => $this->type->value,
        ];
    }
} 