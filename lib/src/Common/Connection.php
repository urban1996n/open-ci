<?php

namespace App\Common;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Connection extends AMQPStreamConnection
{
    private static ?self $instance = null;

    private AMQPChannel $currentChannel;

    public function __construct(string $amqpHost, int $amqpPort, string $amqpUser, string $amqpPassword)
    {
        parent::__construct($amqpHost, $amqpPort, $amqpUser, $amqpPassword);

        $this->currentChannel = $this->channel();
    }

    public function getCurrentChannel(): ?AMQPChannel
    {
        return $this->currentChannel;
    }
}