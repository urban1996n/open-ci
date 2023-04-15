<?php

namespace App\Job;

use App\Common\Status;
use App\Job\Data\Config;
use App\Job\Data\JobConfigAwareInterface;
use App\Job\Event\JobEvents;
use App\Job\Event\StatusChangeEvent;
use App\Job\Exception\JobRunException;
use App\Job\Logger\Logger;
use App\Pipeline\PipelineFactory;
use App\Resource\Locator;
use Monolog\Level;
use Psr\EventDispatcher\EventDispatcherInterface;

class Job implements JobConfigAwareInterface
{
    private Status $status = Status::Pending;

    public function __construct(
        private readonly Config $config,
        private readonly Executor $executor,
        private readonly Logger $logger,
        private readonly Locator $locator,
        private readonly PipelineFactory $pipelineFactory,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function start(): void
    {
        $logger = $this->logger;
        $logger = function (string $type, string $message) use ($logger): void {
            $logger->log(Level::fromName($type), $message);
        };

        try {
            $this->executor->execute(
                $this->status,
                $this->pipelineFactory->create($this->locator->locatePipelineFileFor($this->getConfig())),
                $logger,
                $this->changeStatusCallback(),
            );
        } catch (\Throwable $exception) {
            $this->status = Status::Failure;
            // Otherwise create translatable exception. - Only pipeline and job exceptions will work as a reason for status change.
            throw new JobRunException($exception->getMessage(), $this->getConfig());
        }
    }

    private function changeStatusCallback(): \Closure
    {
        return function (Status $status) {
            $this->status = $status;
            $this->dispatcher->dispatch(
                new StatusChangeEvent($this->getConfig(), $status),
                JobEvents::JOB_STATUS_CHANGE
            );
        };
    }

    public function getBranch(): string
    {
        return $this->config->getBranch();
    }

    public function getCurrentCommit(): string
    {
        return $this->config->getCommitHash();
    }

    public function getBuildNumber(): int
    {
        return $this->config->getBuildNumber();
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getIdentifier(): string
    {
        return \substr(\md5($this->getBranch() . $this->getCurrentCommit() . $this->getBuildNumber()), 0, 20);
    }

    public function getConfig(): ?Config
    {
        return $this->config;
    }
}
