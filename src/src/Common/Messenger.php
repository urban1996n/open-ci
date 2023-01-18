<?php

namespace App\Common;

use PhpAmqpLib\Message\AMQPMessage;

abstract class Messenger
{
    private Connection $connection;

    public function __construct()
    {
        $this->connection = new Connection();
    }

    public function send(int $queueType): void
    {
        $this->connection->getCurrentChannel();
    }
}