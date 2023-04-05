<?php

namespace App\AMQP\Event;

use PhpAmqpLib\Exception\AMQPExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class AmqpExceptionEvent extends Event
{
    public function __construct(private readonly Request $request, private readonly AMQPExceptionInterface $exception)
    {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getException(): AMQPExceptionInterface
    {
        return $this->exception;
    }
}
