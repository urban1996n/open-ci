<?php

namespace App\Job\Exception;

use App\Common\Exception\JobConfigAwareException;
use App\Job\Config;

abstract class JobConfigException extends \RuntimeException implements JobConfigAwareException
{
    public function __construct(mixed $message, private readonly ?Config $job)
    {
        parent::__construct($message);
    }

    public function getConfig(): ?Config
    {
        return $this->job;
    }
}
