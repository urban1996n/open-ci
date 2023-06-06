<?php

namespace App\Common\Exception;

use App\Job\Config;
use App\Job\JobConfigAwareInterface;

interface JobConfigAwareException extends JobConfigAwareInterface, \Throwable
{
    public function __construct(mixed $message, Config $job);
}
