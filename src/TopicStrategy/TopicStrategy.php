<?php

declare(strict_types=1);

namespace Crutch\RetryConsumerHandler\TopicStrategy;

interface TopicStrategy
{
    public function getRetryTopic(string $originalTopic): string;
}
