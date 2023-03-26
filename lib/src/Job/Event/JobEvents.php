<?php

namespace App\Job\Event;

enum JobEvents: string
{
    case JOB_ERROR = 'job.event.error';
    case JOB_CREATED = 'job.event.created';
    case JOB_STATUS_CHANGE = 'job.event.status_change';
}
