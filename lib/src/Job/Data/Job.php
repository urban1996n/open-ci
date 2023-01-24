<?php

namespace App\Job\Data;

class Job
{
    private string $currentCommit;

    private bool $state = false;

    public function __construct(string $currentCommit)
    {
        $this->currentCommit = $currentCommit;
    }

    public function getCurrentCommit(): string
    {
        return $this->currentCommit;
    }
}