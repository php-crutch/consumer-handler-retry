<?php

declare(strict_types=1);

namespace Crutch\RetryConsumerHandler\DelayStrategy;

final class FibonacciStrategy implements DelayStrategy
{
    private float $factor;

    public function __construct(float $factor = 1.0)
    {
        $this->factor = $factor;
    }

    public function calculateDelay(int $attempt): float
    {
        $fib = $this->fib($attempt);
        $delay = (float)$fib * $this->factor;
        if ($delay < 0) {
            return 0.0;
        }
        return $delay;
    }

    private function fib(int $i): int
    {
        $fib = [
            0,
            1,
            1,
            2,
            3,
            5,
            8,
            13,
            21,
            34,
            55,
            89,
            144,
            233,
            377,
            610,
            987,
            1597,
            2584,
            4181,
            6765,
            10946,
            17711,
            28657,
            46368,
            75025,
            121393,
        ];
        if ($i <= 0 ) return 0;
        if (array_key_exists($i, $fib)) {
            return $fib[$i];
        }
        return $this->fib($i - 1) + $this->fib($i - 2);
    }
}
