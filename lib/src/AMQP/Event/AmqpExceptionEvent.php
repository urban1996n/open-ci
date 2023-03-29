<?php

namespace App\AMQP\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class AmqpExceptionEvent extends Event
{
    public function __construct(private readonly Request $request, private readonly \Throwable $exception)
    {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }
}
