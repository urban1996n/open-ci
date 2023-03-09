<?php

namespace App\AMQP;

use PhpAmqpLib\Message\AMQPMessage;

abstract class AbstractConsumer
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function consume(\Closure $callback): void
    {
        $channel = $this->connection->getCurrentChannel();
        $queue   = $this->getQueue()->value;

        $channel->queue_bind($queue, $this->getExchange()->value);
        $this->connection->getCurrentChannel()->basic_consume($queue, '', true, false, false, false, $callback);
        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    abstract protected function getQueue(): Queue;

    abstract protected function getExchange(): Exchange;
}
