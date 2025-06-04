<?php

namespace Ronydebnath\MCP\Shared;

class Progress
{
    private float $total;
    private float $current = 0;
    private ?string $status = null;
    private array $metadata = [];

    public function __construct(float $total = 100.0)
    {
        $this->total = $total;
    }

    /**
     * Update the current progress
     *
     * @param float $value
     * @param string|null $status
     * @param array $metadata
     * @return void
     */
    public function update(float $value, ?string $status = null, array $metadata = []): void
    {
        $this->current = min($value, $this->total);
        $this->status = $status;
        $this->metadata = array_merge($this->metadata, $metadata);
    }

    /**
     * Increment the current progress
     *
     * @param float $value
     * @param string|null $status
     * @param array $metadata
     * @return void
     */
    public function increment(float $value = 1.0, ?string $status = null, array $metadata = []): void
    {
        $this->update($this->current + $value, $status, $metadata);
    }

    /**
     * Get the current progress as a percentage
     *
     * @return float
     */
    public function getPercentage(): float
    {
        return ($this->current / $this->total) * 100;
    }

    /**
     * Get the current progress value
     *
     * @return float
     */
    public function getCurrent(): float
    {
        return $this->current;
    }

    /**
     * Get the total progress value
     *
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * Get the current status
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Get the metadata
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Check if the progress is complete
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->current >= $this->total;
    }

    /**
     * Reset the progress
     *
     * @return void
     */
    public function reset(): void
    {
        $this->current = 0;
        $this->status = null;
        $this->metadata = [];
    }

    /**
     * Get the progress as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'current' => $this->current,
            'total' => $this->total,
            'percentage' => $this->getPercentage(),
            'status' => $this->status,
            'metadata' => $this->metadata,
            'is_complete' => $this->isComplete(),
        ];
    }
} 