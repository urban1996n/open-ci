<?php

namespace App\Job;

interface JobConfigAwareInterface
{
    public function getConfig(): ?Config;
}
