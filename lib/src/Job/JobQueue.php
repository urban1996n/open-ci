<?php

namespace App\Job;

use App\AMQP\AbstractQueue;

class JobQueue extends AbstractQueue
{
    public function getName(): string
    {
        return 'job_queue';
    }
}
