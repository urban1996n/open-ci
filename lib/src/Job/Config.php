<?php

namespace App\Job;

class Config
{
    public function __construct(
        private readonly string $branch,
        private readonly string $commitHash,
        private readonly int $buildNumber
    ) {
    }

    public function getBuildNumber(): int
    {
        return $this->buildNumber;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }

    public function getCommitHash(): string
    {
        return $this->commitHash;
    }

    public function getIdentifier(): string
    {
        return \substr(\md5($this->getBranch() . $this->getCommitHash() . $this->getBuildNumber()), 0, 20);
    }
}
