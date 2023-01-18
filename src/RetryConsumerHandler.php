<?php

declare(strict_types=1);

namespace Crutch\RetryConsumerHandler;

use Crutch\Consumer\ConsumerHandler;
use Crutch\RetryConsumerHandler\DelayStrategy\AttemptStrategy;
use Crutch\RetryConsumerHandler\DelayStrategy\DelayStrategy;
use Crutch\RetryConsumerHandler\TopicStrategy\OriginalTopicStrategy;
use Crutch\RetryConsumerHandler\TopicStrategy\TopicStrategy;
use Crutch\Producer\Producer;
use Throwable;

final class RetryConsumerHandler implements ConsumerHandler
{
    private ConsumerHandler $handler;
    private Producer $producer;
    private TopicStrategy $topicStrategy;
    private DelayStrategy $delayStrategy;
    private int $maxAttempt;

    public function __construct(
        ConsumerHandler $handler,
        Producer $producer,
        ?TopicStrategy $topicStrategy = null,
        ?DelayStrategy $delayStrategy = null,
        int $maxAttempt = 10
    ) {
        $this->handler = $handler;
        $this->producer = $producer;
        $this->topicStrategy = $topicStrategy ?? new OriginalTopicStrategy();
        $this->delayStrategy = $delayStrategy ?? new AttemptStrategy();
        $this->maxAttempt = $maxAttempt;
    }

    /**
     * @param string $message
     * @param string $topic
     * @return void
     * @throws Throwable
     */
    public function handle(string $message, string $topic): void
    {
        [$attempt, $reason, $original] = $this->checkForFailed($message);
        try {
            $this->handler->handle($original, $topic);
        } catch (Throwable $exception) {
            if ($exception->getMessage() !== $reason) {
                $attempt = 1;
            } else {
                $attempt++;
            }
            if ($attempt > $this->maxAttempt) {
                return;
            }
            $failedEvent = json_encode([
                'event' => 'message.failed',
                'data' => [
                    'original' => $original,
                    'attempt' => $attempt,
                    'reason' => $exception->getMessage(),
                ],
            ]);
            $delay = $this->delayStrategy->calculateDelay($attempt);

            $failedTopic = $this->topicStrategy->getRetryTopic($topic);
            $this->producer->produce($failedEvent, $failedTopic, max(0.0, $delay));
        }
    }

    /**
     * @return array{0: int, 1: null|string, 2: string}
     */
    private function checkForFailed(string $message): array
    {
        $event = json_decode($message);
        if (
            !is_object($event)
            || !property_exists($event, 'event')
            || $event->event !== 'message.failed'
            || !property_exists($event, 'data')
            || !is_object($event->data)
            || !property_exists($event->data, 'attempt')
            || !is_int($event->data->attempt)
            || !property_exists($event->data, 'reason')
            || !is_string($event->data->reason)
            || !property_exists($event->data, 'original')
            || !is_string($event->data->original)
        ) {
            return [0, null, $message];
        }
        return [$event->data->attempt, $event->data->reason, $event->data->original];
    }
}
