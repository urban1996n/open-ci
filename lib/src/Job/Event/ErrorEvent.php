<?php

namespace App\Job\Event;

use App\Job\Data\Config;
use App\Job\Exception\JobException;
use App\Job\Job;
use App\Pipeline\Exception\PipelineException;

class ErrorEvent extends JobEvent
{
    public function __construct(Config $jobConfig, private readonly JobException $exception)
    {
        parent::__construct($jobConfig);
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }
}
