<?php

declare(strict_types=1);

namespace Crutch\RetryConsumerHandler\DelayStrategy;

interface DelayStrategy
{
    public function calculateDelay(int $attempt): float;
}
