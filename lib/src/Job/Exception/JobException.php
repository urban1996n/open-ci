<?php

namespace App\Job\Exception;

use App\Job\Job;

abstract class JobException extends \RuntimeException
{
    public function __construct(string $message, private readonly ?Job $job)
    {
        parent::__construct($message);
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }
}
