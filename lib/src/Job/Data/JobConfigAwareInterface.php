<?php

namespace App\Job\Data;

interface JobConfigAwareInterface
{
    public function getConfig(): ?Config;
}
