<?php

namespace App\Job\Event;

use App\Job\Data\Config;
use App\Job\Data\JobConfigAwareInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class JobEvent extends Event implements JobConfigAwareInterface
{
    public function __construct(private readonly Config $jobConfig)
    {
    }

    final public function getConfig(): Config
    {
        return $this->jobConfig;
    }
}
