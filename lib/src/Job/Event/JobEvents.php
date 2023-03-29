<?php

namespace App\Job\Event;

interface JobEvents
{
    public const JOB_ERROR         = 'job.event.error';
    public const JOB_CREATED       = 'job.event.created';
    public const JOB_STATUS_CHANGE = 'job.event.status_change';
}
