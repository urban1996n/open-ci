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

//         We do not want to requeue the same build, same identifier means same commit, and same build number;
        if ($this->registry->inQueue($jobConfig)) {
            dump('in progress');

            return;
        }

        $this->registry->add($jobConfig);
        $this->runner->run($this->jobFactory->create($this->registry->next($jobConfig)));
        $this->registry->release($jobConfig);
    }
}
