<?php

namespace App\Job\Data;

class Job
{
    private string $currentCommit;

    private bool $state = false;

    private string $branch;

    public function __construct(string $branch, string $currentCommit)
    {
        $this->branch        = $branch;
        $this->currentCommit = $currentCommit;
    }

    public function getCurrentCommit(): string
    {
        return $this->currentCommit;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }
}