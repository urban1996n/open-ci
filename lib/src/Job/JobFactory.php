<?php

namespace App\Job;

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
        private readonly PipelineFactory $pipelineFactory
    ) {
    }

    // Removed hard typing so all future errors can be globally handled here.
    public function create($branch, $commitHash, $buildNumber): ?Job
    {
        $logger = $this->factory->create($branch, $commitHash, $buildNumber);

        try {
            $job = new Job(
                $branch,
                $commitHash,
                $buildNumber,
                $this->executor,
                $logger,
                $this->locator,
                $this->pipelineFactory
            );
        } catch (\Throwable $exception) {
            throw new JobCreationException($exception->getMessage(), null);
        }

        return $job;
    }
}
