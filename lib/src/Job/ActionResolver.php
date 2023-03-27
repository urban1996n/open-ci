<?php

namespace App\Job;

use App\Job\Data\Config;
use App\Runner\Runner;

class ActionResolver
{
    public function __construct(
        private readonly Registry $registry,
        private readonly Runner $runner,
        private readonly JobFactory $jobFactory
    ) {
    }

    public function resolve(Config $jobConfig): void
    {
        while ($this->registry->isLocked()) {
            usleep(10);
        }

        $this->registry->tryAdd($jobConfig);
        try {
            $this->runner->run($this->jobFactory->create($this->registry->next($jobConfig)));
        } finally {
            $this->registry->release($jobConfig);
        }
    }
}
