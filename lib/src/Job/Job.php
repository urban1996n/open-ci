<?php

namespace App\Job;

use App\Common\Status;
use App\Job\Logger\Logger;
use Monolog\Level;

class Job
{
    private Status|null $status = null;

    public function __construct(
        private readonly string $branch,
        private readonly string $currentCommit,
        private readonly int $buildNumber,
        private readonly Executor $executor,
        private readonly Logger $logger
    ) {
    }

    public function start(): void
    {
        $logger = $this->logger;
        $logger = function (string $type, string $message) use ($logger): void {
            $logger->log(Level::fromName($type), $message);
        };

        $this->executor->execute($logger);
        $this->status = $this->executor->getStatus();
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
}
