<?php

namespace App\Resource;

use App\Job\Job;
use Symfony\Component\Config\FileLocator;

class Locator
{
    public function __construct(
        private readonly string $rootDir,
        private readonly string $githubOwner,
        private readonly string $githubRepository
    ) {
    }

    public function locateTempDir(): string
    {
        return $this->rootDir . '/tmp';
    }

    public function locateExecDir(): string
    {
        return $this->rootDir . '/../src';
    }

    public function getTempDirForJob(Job $job): string
    {
        return $this->locateTempDir() . '/' . $this->buildDestinationPath($job);
    }

    public function getTemporaryRepoArchiveFile(Job $job): string
    {
        return $this->getTempDirForJob($job) . '/' . $job->getIdentifier() . '.zip';
    }

    public function getExecDirForJob(Job $job): string
    {
        return $this->locateExecDir() . '/' . $this->buildDestinationPath($job);
    }

    public function getUnpackedRepoDirForJob(Job $job): string
    {
        return $this->getExecDirForJob($job)
            . '/' . $this->githubOwner . '-' . $this->githubRepository . '-' . $job->getCurrentCommit();
    }

    public function getPipelineFileForJob(Job $job): string
    {
        return $this->getUnpackedRepoDirForJob($job) . '/pipeline.json';
    }

    private function buildDestinationPath(Job $job): string
    {
        return $this->githubOwner
            . '/' . $this->githubRepository
            . '/' . $job->getBranch()
            . '/' . $job->getCurrentCommit()
            . '/' . $job->getBuildNumber();
    }
}
