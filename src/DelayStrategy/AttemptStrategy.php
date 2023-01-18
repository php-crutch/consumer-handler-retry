<?php

declare(strict_types=1);

namespace Crutch\RetryConsumerHandler\DelayStrategy;

final class AttemptStrategy implements DelayStrategy
{
    private float $factor;

    public function __construct(float $factor = 1.0)
    {
        $this->factor = $factor;
    }

    public function calculateDelay(int $attempt): float
    {
        $delay = (float)$attempt * $this->factor;
        if ($delay < 0) {
            return 0.0;
        }
        return $delay;
    }
}
