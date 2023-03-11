<?php

namespace App\Runner;

use App\AMQP\AbstractConsumer;
use App\AMQP\Exchange;
use App\AMQP\Queue;

class Consumer extends AbstractConsumer
{
    protected function getQueue(): Queue
    {
        return Queue::Job;
    }

    protected function getExchange(): Exchange
    {
        return Exchange::Local;
    }
}
