<?php

namespace App\Job;

use App\Job\Event\CreatedEvent;
use App\Job\Event\ErrorEvent;
use App\Job\Event\JobEvents;
use App\Job\Exception\JobCreationException;
use App\Job\Logger\LoggerFactory;
use App\Pipeline\PipelineFactory;
use App\Resource\Locator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class JobFactory
{
    public function __construct(
        private readonly LoggerFactory $factory,
        private readonly Executor $executor,
        private readonly Locator $locator,
        private readonly PipelineFactory $pipelineFactory,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function create(Config $config): ?Job
    {
        $logger = $this->factory->create($config);
        $job    = null;

        try {
            $job = new Job(
                $config,
                $this->executor,
                $logger,
                $this->locator,
                $this->pipelineFactory,
                $this->dispatcher
            );

            $this->dispatcher->dispatch(
                new CreatedEvent($config),
                JobEvents::JOB_CREATED
            );
        } catch (\Throwable $exception) {
            $this->dispatcher->dispatch(
                new ErrorEvent($config, new JobCreationException($exception->getMessage(), null)),
                JobEvents::JOB_ERROR
            );
        }

        return $job;
    }
}
