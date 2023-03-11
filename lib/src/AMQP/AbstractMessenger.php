<?php

namespace App\AMQP;

use PhpAmqpLib\Message\AMQPMessage;

abstract class AbstractMessenger
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function send(AMQPMessage $message): void
    {
        $this->connection->getCurrentChannel()->basic_publish(
            $message,
            $this->getExchange()->value,
            $this->getQueue()->value
        );
    }

    abstract protected function getQueue(): Queue;

    abstract protected function getExchange(): Exchange;
}
