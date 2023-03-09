<?php

namespace App\Job\Logger;

use App\Job\Job;

class LoggerFactory
{
    public function __construct(private readonly string $rootDir)
    {
    }

    public function create(string $branch, string $commitHash, int $buildNumber): Logger
    {
        return new Logger($this->rootDir, $branch, $commitHash, $buildNumber);
    }
}
