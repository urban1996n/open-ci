<?php

namespace App\Job;

use App\Job\Logger\LoggerFactory;
use App\Pipeline\PipelineFactory;
use App\Resource\Locator;

class JobFactory
{
    public function __construct(
        private readonly LoggerFactory $factory,
        private readonly Executor $executor,
        private readonly Locator $locator,
        private readonly PipelineFactory $pipelineFactory
    ) {
    }

    public function create(string $branch, string $commitHash, int $buildNumber): Job
    {
        $logger = $this->factory->create($branch, $commitHash, $buildNumber);

        return new Job($branch, $commitHash, $buildNumber, $this->executor, $logger, $this->locator, $this->pipelineFactory);
    }
}
