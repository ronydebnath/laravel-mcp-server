<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Ronydebnath\MCP\Shared\Progress;

// Create a new progress instance
$progress = new Progress();

// Simulate a long-running operation
echo "Starting operation...\n";

// Update progress with status
$progress->update(0, "Initializing...", ['step' => 'start']);
echo "Progress: {$progress->getPercentage()}% - {$progress->getStatus()}\n";

// Simulate some work
sleep(1);
$progress->update(25, "Processing data...", ['step' => 'process']);
echo "Progress: {$progress->getPercentage()}% - {$progress->getStatus()}\n";

// Increment progress
sleep(1);
$progress->increment(25);
echo "Progress: {$progress->getPercentage()}% - {$progress->getStatus()}\n";

// Update with new status
sleep(1);
$progress->update(75, "Finalizing...", ['step' => 'finish']);
echo "Progress: {$progress->getPercentage()}% - {$progress->getStatus()}\n";

// Complete the operation
sleep(1);
$progress->update(100, "Complete!", ['step' => 'complete']);
echo "Progress: {$progress->getPercentage()}% - {$progress->getStatus()}\n";

// Show metadata
echo "Metadata: " . json_encode($progress->getMetadata()) . "\n";

// Reset progress
$progress->reset();
echo "Progress reset. Current progress: {$progress->getPercentage()}%\n"; 