<?php

namespace App\Job;

class Job
{
    private bool $finished = false;

    private ?bool $success = null;

    public function __construct(private readonly string $branch, private readonly string $currentCommit)
    {

    }
}
