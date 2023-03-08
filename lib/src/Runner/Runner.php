<?php

namespace App\Runner;

use App\Job\Executor;
use App\Job\Job;
use App\Job\Logger\LoggerFactory;

class Runner
{
    private bool $occupied = false;

    public function __construct(private readonly Executor $executor, private readonly LoggerFactory $factory)
    {

    }

    public function run(Job $job): void
    {
        $this->occupied = true;

        $job->setUp($this->executor, $this->factory->create($job));
        $job->start();

        $this->occupied = false;
    }

    public function isOccupied(): bool
    {
        return $this->occupied;
    }
}
