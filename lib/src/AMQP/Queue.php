<?php

namespace App\AMQP;

enum Queue: string {
    case Job = 'job_queue';
}
