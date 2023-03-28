<?php

namespace App\AMQP;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Connection extends AMQPStreamConnection
{
    private ?AMQPChannel $currentChannel = null;

    private array $queues    = [];
    private array $exchanges = [];

    private function defineQueues(): void
    {
        foreach (Queue::cases() as $queue) {
            $this->currentChannel->queue_declare($queue->value);
            $this->queues[] = $queue->value;
        }
    }

    private function defineExchanges(): void
    {
        foreach (Exchange::cases() as $exchange) {
            $this->currentChannel->exchange_declare($exchange->value, 'fanout');
            $this->exchanges[] = $exchange->value;
        }
    }

    public function __construct(string $amqpHost, int $amqpPort, string $amqpUser, string $amqpPassword)
    {
        parent::__construct(
            $amqpHost,
            $amqpPort,
            $amqpUser,
            $amqpPassword,
            '/',
            false,
            'AMQPLAIN',
            null,
            'pl_PL',
            120,
            120,
            null,
            true
        );

        $this->currentChannel = $this->currentChannel ?: $this->channel();
        $this->defineQueues();
        $this->defineExchanges();
    }

    public function getCurrentChannel(): AMQPChannel
    {
        return $this->currentChannel;
    }

    public function isInitialized(): bool
    {
        return \count($this->exchanges) === \count(Exchange::cases())
            && \count($this->queues) === \count(Queue::cases());
    }
}
