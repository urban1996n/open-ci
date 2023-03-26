<?php

namespace App\Job\Event;

use App\Common\Status;
use App\Job\Data\Config;

class StatusChangeEvent extends JobEvent
{
    public function __construct(Config $jobConfig, private readonly Status $status)
    {
        parent::__construct($jobConfig);
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
