<?php

namespace App\Http;

use App\AMQP\AbstractMessenger;
use App\AMQP\Connection;
use App\AMQP\Exchange;
use App\AMQP\Queue;

class JobMessenger extends AbstractMessenger
{
    protected function getExchange(): Exchange
    {
        return Exchange::Local;
    }

    protected function getQueue(): Queue
    {
        return Queue::Job;
    }
}
