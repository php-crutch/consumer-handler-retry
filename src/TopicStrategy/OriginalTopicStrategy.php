<?php

declare(strict_types=1);

namespace Crutch\RetryConsumerHandler\TopicStrategy;

final class OriginalTopicStrategy implements TopicStrategy
{
    public function getRetryTopic(string $originalTopic): string
    {
        return $originalTopic;
    }
}
