<?php

namespace App\Job\Event;

use App\Job\Job;
use Symfony\Contracts\EventDispatcher\Event;

abstract class JobEvent extends Event
{
    public function __construct(private readonly Job $job)
    {
    }

    final public function getJob(): Job
    {
        return $this->job;
    }
}
