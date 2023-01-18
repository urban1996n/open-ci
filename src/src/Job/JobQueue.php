<?php

namespace App\Job;

use App\Common\AbstractQueue;

class JobQueue extends AbstractQueue
{
    public function getName(): string
    {
        return 'job_queue';
    }
}