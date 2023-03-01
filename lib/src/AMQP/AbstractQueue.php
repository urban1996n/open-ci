<?php

namespace App\AMQP;

use PhpAmqpLib\Channel\AMQPChannel;

abstract class AbstractQueue
{
    protected AMQPChannel $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    public abstract function getName(): string;

    public function open(): void
    {
        $this->channel->queue_declare($this->getName());
    }

    public function close(): void
    {

    }
}
