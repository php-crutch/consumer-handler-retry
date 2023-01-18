<?php

declare(strict_types=1);

namespace Crutch\RetryConsumerHandler\TopicStrategy;

final class HardTopicStrategy implements TopicStrategy
{
    private string $failedTopic;

    public function __construct(string $failedTopic)
    {
        $this->failedTopic = $failedTopic;
    }

    public function getRetryTopic(string $originalTopic): string
    {
        return $this->failedTopic;
    }
}
