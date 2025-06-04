<?php

namespace Ronydebnath\MCP\Tests\Shared;

use PHPUnit\Framework\TestCase;
use Ronydebnath\MCP\Shared\Progress;

class ProgressTest extends TestCase
{
    private Progress $progress;

    protected function setUp(): void
    {
        parent::setUp();
        $this->progress = new Progress(100.0);
    }

    public function test_can_update_progress(): void
    {
        $this->progress->update(50.0, 'Processing', ['step' => 'analysis']);
        
        $this->assertEquals(50.0, $this->progress->getCurrent());
        $this->assertEquals('Processing', $this->progress->getStatus());
        $this->assertEquals(['step' => 'analysis'], $this->progress->getMetadata());
    }

    public function test_can_increment_progress(): void
    {
        $this->progress->update(40.0);
        $this->progress->increment(10.0, 'Moving forward', ['step' => 'next']);
        
        $this->assertEquals(50.0, $this->progress->getCurrent());
        $this->assertEquals('Moving forward', $this->progress->getStatus());
        $this->assertEquals(['step' => 'next'], $this->progress->getMetadata());
    }

    public function test_cannot_exceed_total(): void
    {
        $this->progress->update(150.0);
        
        $this->assertEquals(100.0, $this->progress->getCurrent());
    }

    public function test_calculates_percentage_correctly(): void
    {
        $this->progress->update(75.0);
        
        $this->assertEquals(75.0, $this->progress->getPercentage());
    }

    public function test_can_check_completion(): void
    {
        $this->assertFalse($this->progress->isComplete());
        
        $this->progress->update(100.0);
        $this->assertTrue($this->progress->isComplete());
    }

    public function test_can_reset_progress(): void
    {
        $this->progress->update(50.0, 'Processing', ['step' => 'analysis']);
        $this->progress->reset();
        
        $this->assertEquals(0.0, $this->progress->getCurrent());
        $this->assertNull($this->progress->getStatus());
        $this->assertEmpty($this->progress->getMetadata());
    }

    public function test_can_convert_to_array(): void
    {
        $this->progress->update(50.0, 'Processing', ['step' => 'analysis']);
        
        $array = $this->progress->toArray();
        
        $this->assertEquals([
            'current' => 50.0,
            'total' => 100.0,
            'percentage' => 50.0,
            'status' => 'Processing',
            'metadata' => ['step' => 'analysis'],
            'is_complete' => false,
        ], $array);
    }
} 