<?php

namespace App\Job;

use App\Common\Status;
use App\Job\Exception\JobRunException;
use App\Job\Logger\Logger;
use App\Pipeline\Exception\PipelineException;
use App\Pipeline\PipelineFactory;
use App\Resource\Locator;
use Monolog\Level;

class Job
{
    private Status $status = Status::Pending;

    public function __construct(
        private readonly string $branch,
        private readonly string $currentCommit,
        private readonly int $buildNumber,
        private readonly Executor $executor,
        private readonly Logger $logger,
        private readonly Locator $locator,
        private readonly PipelineFactory $pipelineFactory
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
                $logger,
                $this->status,
                $this->pipelineFactory->create($this->locator->getPipelineFileForJob($this))
            );
        } catch (\Throwable $exception) {
            $this->status = Status::Failure;
            // Otherwise create translatable exception. - Only pipeline and job exceptions will work as a reason for status change.
            throw new JobRunException($exception->getMessage(), $this);
        }
    }

    public function isFinished(): bool
    {
        return !!$this?->status?->isFinished();
    }

    public function getBranch(): string
    {
        return $this->branch;
    }

    public function getCurrentCommit(): string
    {
        return $this->currentCommit;
    }

    public function getBuildNumber(): int
    {
        return $this->buildNumber;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getIdentifier(): string
    {
        return \substr(\md5($this->getBranch() . $this->getCurrentCommit() . $this->getBuildNumber()), 0, 20);
    }
}
