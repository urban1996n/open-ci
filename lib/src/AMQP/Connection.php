<?php

namespace App\AMQP;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Connection extends AMQPStreamConnection
{
    private ?AMQPChannel $currentChannel = null;

    private function defineQueues(): void
    {
        foreach (Queue::cases() as $queue) {
            $this->currentChannel->queue_declare($queue->value, true);
        }
    }

    private function defineExchanges(): void
    {
        foreach (Exchange::cases() as $case) {
            $this->currentChannel->exchange_declare($case->value, 'fanout');
        }
    }

    public function __construct(string $amqpHost, int $amqpPort, string $amqpUser, string $amqpPassword)
    {
        parent::__construct($amqpHost, $amqpPort, $amqpUser, $amqpPassword);

        $this->currentChannel = $this->currentChannel ?: $this->channel();
        $this->defineQueues();
        $this->defineExchanges();
    }

    public function getCurrentChannel(): AMQPChannel
    {
        return $this->currentChannel;
    }
}
