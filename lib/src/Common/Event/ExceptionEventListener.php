<?php

namespace App\Common\Event;

use App\AMQP\Event\AmqpEvents;
use App\AMQP\Event\AmqpExceptionEvent;
use App\Job\Event\ErrorEvent;
use App\Job\Event\JobEvents;
use App\Job\Exception\JobException;
use App\Storage\Redis;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Exception\AMQPIOException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException')]
#[AsEventListener(event: 'console.error', method: 'onConsoleError')]
class ExceptionEventListener
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly LoggerInterface $logger
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $this->logger->error($exception->getMessage());

        if ($exception instanceof AMQPExceptionInterface) {
            $event = new AmqpExceptionEvent($event->getRequest(), $exception);
            $this->dispatcher->dispatch($event, AmqpEvents::AMQP_EXCEPTION);
        }
    }

    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        $exception = $event->getError();
        $this->logger->error($exception->getMessage());

        if ($exception instanceof JobException && $exception->getJob()) {
            $this->dispatcher->dispatch(
                new ErrorEvent($exception->getJob()->getConfig(), $exception),
                JobEvents::JOB_ERROR->value
            );
        }

        $event->getOutput()->writeln($exception->getMessage());
        $event->getCommand()->getApplication()->run();
    }
}
