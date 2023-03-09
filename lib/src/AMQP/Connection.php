<?php

namespace App\AMQP;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Connection extends AMQPStreamConnection
{
    private ?AMQPChannel $currentChannel = null;

    public function defineQueues(): void
    {
        foreach (Queue::cases() as $queue) {
            $this->currentChannel->queue_declare($queue, true);
        }
    }

    public function __construct(string $amqpHost, int $amqpPort, string $amqpUser, string $amqpPassword)
    {
        parent::__construct($amqpHost, $amqpPort, $amqpUser, $amqpPassword);

        $this->currentChannel = $this->currentChannel ?: $this->channel();
        $this->defineQueues();
    }

    public function getCurrentChannel(): AMQPChannel
    {
        return $this->currentChannel;
    }
}
