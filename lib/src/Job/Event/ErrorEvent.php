<?php

namespace App\Job\Event;

use App\Job\Data\Config;
use App\Job\Exception\JobConfigException;

class ErrorEvent extends JobEvent
{
    public function __construct(Config $jobConfig, private readonly JobConfigException $exception)
    {
        parent::__construct($jobConfig);
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }
}
