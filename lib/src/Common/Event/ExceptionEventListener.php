<?php

namespace App\Common\Event;

use App\Job\Event\ErrorEvent;
use App\Job\Exception\JobException;
use App\Pipeline\Exception\PipelineException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException')]
#[AsEventListener(event: 'console.error', method: 'onKernelException')]
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

        if ($exception instanceof JobException) {
            $this->dispatcher->dispatch(new ErrorEvent($exception->getJob(), $exception));
        }
    }

    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        $exception = $event->getError();

        if ($exception instanceof JobException) {
            $this->dispatcher->dispatch(new ErrorEvent($exception->getJob(), $exception));
        } else {
            $event->getCommand()->getApplication()->run();
        }
    }
}
