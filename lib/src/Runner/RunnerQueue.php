<?php

namespace App\Runner;

use App\Common\AbstractQueue;

class RunnerQueue extends AbstractQueue
{
    public function getName(): string
    {
        return 'runner_queue';
    }
}