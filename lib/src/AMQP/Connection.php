<?php

namespace App\AMQP;

use App\AMQP\Event\AmqpEvents;
use App\AMQP\Event\AmqpIOConnectionEvent;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

    public function __construct(
        string $amqpHost,
        int $amqpPort,
        string $amqpUser,
        string $amqpPassword,
        EventDispatcherInterface $eventDispatcher
    ) {
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
        $eventDispatcher->dispatch(new AmqpIOConnectionEvent(), AmqpEvents::AMQP_IO_CONNECTION);
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
