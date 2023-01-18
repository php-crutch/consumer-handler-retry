<?php

declare(strict_types=1);

namespace Crutch\RetryConsumerHandler\DelayStrategy;

final class HardDelayStrategy implements DelayStrategy
{
    private float $delay;

    public function __construct(float $delay)
    {
        $this->delay = max(0.0, $delay);
    }

    public function calculateDelay(int $attempt): float
    {
        return $this->delay;
    }
}
