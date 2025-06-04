<?php

namespace Ronydebnath\MCP\Tests\Shared;

use PHPUnit\Framework\TestCase;
use Ronydebnath\MCP\Shared\Memory;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

class MemoryTest extends TestCase
{
    private Memory $memory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memory = new Memory(3); // Small max size for testing
    }

    public function test_can_add_message(): void
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello',
            type: MessageType::TEXT
        );

        $this->memory->add($message);
        $this->assertCount(1, $this->memory->getAll());
    }

    public function test_can_add_multiple_messages(): void
    {
        $messages = [
            new Message(role: Role::USER, content: 'First', type: MessageType::TEXT),
            new Message(role: Role::ASSISTANT, content: 'Second', type: MessageType::TEXT),
        ];

        $this->memory->addMultiple($messages);
        $this->assertCount(2, $this->memory->getAll());
    }

    public function test_respects_max_size(): void
    {
        $messages = [
            new Message(role: Role::USER, content: 'First', type: MessageType::TEXT),
            new Message(role: Role::USER, content: 'Second', type: MessageType::TEXT),
            new Message(role: Role::USER, content: 'Third', type: MessageType::TEXT),
            new Message(role: Role::USER, content: 'Fourth', type: MessageType::TEXT),
        ];

        $this->memory->addMultiple($messages);
        $this->assertCount(3, $this->memory->getAll());
        $this->assertEquals('Second', $this->memory->getAll()[0]->content);
    }

    public function test_can_get_messages_by_role(): void
    {
        $messages = [
            new Message(role: Role::USER, content: 'User message', type: MessageType::TEXT),
            new Message(role: Role::ASSISTANT, content: 'Assistant message', type: MessageType::TEXT),
            new Message(role: Role::USER, content: 'Another user message', type: MessageType::TEXT),
        ];

        $this->memory->addMultiple($messages);
        $userMessages = $this->memory->getByRole(Role::USER);
        $this->assertCount(2, $userMessages);
        $this->assertEquals('User message', $userMessages[0]->content);
    }

    public function test_can_get_messages_by_type(): void
    {
        $messages = [
            new Message(role: Role::USER, content: 'Text message', type: MessageType::TEXT),
            new Message(role: Role::USER, content: 'Image message', type: MessageType::IMAGE),
            new Message(role: Role::USER, content: 'Another text message', type: MessageType::TEXT),
        ];

        $this->memory->addMultiple($messages);
        $textMessages = $this->memory->getByType(MessageType::TEXT);
        $this->assertCount(2, $textMessages);
        $this->assertEquals('Text message', $textMessages[0]->content);
    }

    public function test_can_clear_messages(): void
    {
        $messages = [
            new Message(role: Role::USER, content: 'First', type: MessageType::TEXT),
            new Message(role: Role::USER, content: 'Second', type: MessageType::TEXT),
        ];

        $this->memory->addMultiple($messages);
        $this->assertCount(2, $this->memory->getAll());
        
        $this->memory->clear();
        $this->assertCount(0, $this->memory->getAll());
    }

    public function test_can_check_memory_size(): void
    {
        $this->assertEquals(0, $this->memory->size());
        
        $this->memory->add(new Message(role: Role::USER, content: 'Test', type: MessageType::TEXT));
        $this->assertEquals(1, $this->memory->size());
    }

    public function test_can_check_if_memory_is_empty(): void
    {
        $this->assertTrue($this->memory->isEmpty());
        
        $this->memory->add(new Message(role: Role::USER, content: 'Test', type: MessageType::TEXT));
        $this->assertFalse($this->memory->isEmpty());
    }

    public function test_can_check_if_memory_is_full(): void
    {
        $this->assertFalse($this->memory->isFull());
        
        $messages = [
            new Message(role: Role::USER, content: 'First', type: MessageType::TEXT),
            new Message(role: Role::USER, content: 'Second', type: MessageType::TEXT),
            new Message(role: Role::USER, content: 'Third', type: MessageType::TEXT),
        ];

        $this->memory->addMultiple($messages);
        $this->assertTrue($this->memory->isFull());
    }
} 