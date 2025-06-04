<?php

namespace Ronydebnath\MCP\Tests;

use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\MessageType;
use Ronydebnath\MCP\Types\Role;
use PHPUnit\Framework\TestCase;

class TypesTest extends TestCase
{
    public function test_can_create_message()
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello, MCP!',
            type: MessageType::TEXT
        );

        $this->assertEquals(Role::USER, $message->role);
        $this->assertEquals('Hello, MCP!', $message->content);
        $this->assertEquals(MessageType::TEXT, $message->type);
    }

    public function test_message_serialization()
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello, MCP!',
            type: MessageType::TEXT
        );

        $array = $message->toArray();
        
        $this->assertEquals([
            'role' => 'user',
            'content' => 'Hello, MCP!',
            'type' => 'text'
        ], $array);
    }
} 