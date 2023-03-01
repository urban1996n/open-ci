<?php

namespace App\AMQP;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Connection extends AMQPStreamConnection
{
    private ?AMQPChannel $currentChannel = null;

    public function __construct(string $amqpHost, int $amqpPort, string $amqpUser, string $amqpPassword)
    {
        parent::__construct($amqpHost, $amqpPort, $amqpUser, $amqpPassword);
    }

    public function getCurrentChannel(): AMQPChannel
    {
        return $this->currentChannel = $this->currentChannel ?: $this->channel();
    }
}
