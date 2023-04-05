<?php

namespace App\Common\Exception;

use App\Job\Data\Config;
use App\Job\Data\JobConfigAwareInterface;

interface JobConfigAwareException extends JobConfigAwareInterface, \Throwable
{
    public function __construct(mixed $message, Config $job);
}
