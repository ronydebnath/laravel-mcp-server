<?php

namespace Ronydebnath\MCP\Tests\Shared;

use PHPUnit\Framework\TestCase;
use Ronydebnath\MCP\Shared\Context;
use Ronydebnath\MCP\Shared\Memory;
use Ronydebnath\MCP\Types\Message;
use Ronydebnath\MCP\Types\Role;
use Ronydebnath\MCP\Types\MessageType;

class ContextTest extends TestCase
{
    private Context $context;
    private Memory $memory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memory = new Memory();
        $this->context = new Context($this->memory);
    }

    public function test_can_set_and_get_context_value(): void
    {
        $this->context->set('user_id', 123);
        $this->assertEquals(123, $this->context->get('user_id'));
    }

    public function test_returns_default_value_when_key_not_found(): void
    {
        $this->assertEquals('default', $this->context->get('non_existent', 'default'));
    }

    public function test_can_check_if_key_exists(): void
    {
        $this->assertFalse($this->context->has('user_id'));
        
        $this->context->set('user_id', 123);
        $this->assertTrue($this->context->has('user_id'));
    }

    public function test_can_remove_context_value(): void
    {
        $this->context->set('user_id', 123);
        $this->assertTrue($this->context->has('user_id'));
        
        $this->context->remove('user_id');
        $this->assertFalse($this->context->has('user_id'));
    }

    public function test_can_clear_all_context_data(): void
    {
        $this->context->set('user_id', 123);
        $this->context->set('preferences', ['theme' => 'dark']);
        
        $this->context->clear();
        
        $this->assertEmpty($this->context->getAll());
    }

    public function test_can_get_all_context_data(): void
    {
        $this->context->set('user_id', 123);
        $this->context->set('preferences', ['theme' => 'dark']);
        
        $data = $this->context->getAll();
        
        $this->assertEquals([
            'user_id' => 123,
            'preferences' => ['theme' => 'dark'],
        ], $data);
    }

    public function test_can_get_and_set_memory(): void
    {
        $newMemory = new Memory();
        $this->context->setMemory($newMemory);
        
        $this->assertSame($newMemory, $this->context->getMemory());
    }

    public function test_can_add_message_to_memory(): void
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello',
            type: MessageType::TEXT
        );
        
        $this->context->addMessage($message);
        
        $messages = $this->context->getMessages();
        $this->assertCount(1, $messages);
        $this->assertEquals('Hello', $messages[0]->content);
    }

    public function test_can_clear_messages(): void
    {
        $message = new Message(
            role: Role::USER,
            content: 'Hello',
            type: MessageType::TEXT
        );
        
        $this->context->addMessage($message);
        $this->assertCount(1, $this->context->getMessages());
        
        $this->context->clearMessages();
        $this->assertCount(0, $this->context->getMessages());
    }

    public function test_can_convert_to_array(): void
    {
        $this->context->set('user_id', 123);
        
        $message = new Message(
            role: Role::USER,
            content: 'Hello',
            type: MessageType::TEXT
        );
        $this->context->addMessage($message);
        
        $array = $this->context->toArray();
        
        $this->assertEquals([
            'data' => ['user_id' => 123],
            'messages' => [$message],
        ], $array);
    }
} 