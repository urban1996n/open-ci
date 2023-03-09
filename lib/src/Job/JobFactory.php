<?php

namespace App\Job;

use App\Job\Logger\LoggerFactory;

class JobFactory
{
    public function __construct(private readonly LoggerFactory $factory, private readonly Executor $executor)
    {
    }

    public function create(string $branch, string $commitHash, int $buildNumber): Job
    {
        $logger = $this->factory->create($branch, $commitHash, $buildNumber);

        return new Job($branch, $commitHash, $buildNumber, $this->executor, $logger);
    }
}
