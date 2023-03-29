<?php

namespace App\AMQP\Event;

interface AmqpEvents
{
    /** Alias for  @see AmqpExceptionEvent */
    public const AMQP_EXCEPTION = 'amqp.exception';

    /** Alias for  @see AmqpIOConnectionEvent */
    public const AMQP_IO_CONNECTION = 'amqp.connected';
}
