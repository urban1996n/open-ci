<?php

namespace App\AMQP;

use PhpAmqpLib\Message\AMQPMessage;

abstract class Messenger
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function send(int $queueType): void
    {
        $this->connection->getCurrentChannel();
    }
}
