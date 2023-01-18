# crutch/consumer-handler-retry

consumer handler for retrying failed messages

# Install

```bash
composer require crutch/consumer-handler-retry
```

```php
<?php

/** @var Crutch\Producer\Producer $retryProducer */
/** @var Crutch\Consumer\ConsumerHandler $handler */
/** @var null|Crutch\RetryConsumerHandler\TopicStrategy\TopicStrategy $topicStrategy */
/** @var null|Crutch\RetryConsumerHandler\DelayStrategy\DelayStrategy $delayStrategy */
/** @var null|int $maxAttempts */

$retryHandler = new Crutch\RetryConsumerHandler\RetryConsumerHandler(
    $handler,
    $retryProducer,
    $topicStrategy,
    $delayStrategy,
    $maxAttempts
);

/**
 * if $handler throws exception, $retryProducer produced message to topic, defined by $topicStrategy 
 * with delay, calculated by $selayStrategy.
 */
$retryHandler->handle('message 1', 'one'); // handled by $topicOneHandler
```
