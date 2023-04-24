<?php

namespace App\Common\Event;

use App\AMQP\Event\AmqpEvents;
use App\AMQP\Event\AmqpExceptionEvent;
use App\Job\Event\ErrorEvent;
use App\Job\Event\JobEvents;
use App\Job\Exception\JobConfigException;
use App\Job\Registry;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION, method: 'onKernelException')]
#[AsEventListener(event: ConsoleEvents::ERROR, method: 'onConsoleError')]
class ExceptionEventListener
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly LoggerInterface $logger,
        private readonly Registry $jobRegistry
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

        $config = $exception instanceof JobConfigException && $exception->getConfig() ?
            $exception->getConfig()
            : $this->jobRegistry->getCurrentJob();

        if ($config) {
            $this->dispatcher->dispatch(
                new ErrorEvent($config, $exception),
                JobEvents::JOB_ERROR
            );
        }
    }
}
