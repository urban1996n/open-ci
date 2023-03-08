<?php

namespace App\AMQP;

use PhpAmqpLib\Message\AMQPMessage;

abstract class AbstractMessenger
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Queue $queue,
        private readonly Exchange $exchange = Exchange::Local
    ) {
    }

    public function send(AMQPMessage $message): void
    {
        $this->connection->getCurrentChannel()->basic_publish($message, $this->exchange->value, $this->queue->value);
    }
}
