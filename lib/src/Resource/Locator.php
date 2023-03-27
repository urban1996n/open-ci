<?php

namespace App\Resource;

use App\Job\Data\Config;

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
        return $this->rootDir . '/exec';
    }

    public function getTempDirForJob(Config $jobConfig): string
    {
        return $this->locateTempDir() . '/' . $this->buildDestinationPath($jobConfig);
    }

    public function getTemporaryRepoArchiveFile(Config $jobConfig): string
    {
        return $this->getTempDirForJob($jobConfig) . '/' . $jobConfig->getIdentifier() . '.zip';
    }

    public function getExecDirForJob(Config $jobConfig): string
    {
        return $this->locateExecDir() . '/' . $this->buildDestinationPath($jobConfig);
    }

    public function getUnpackedRepoDirForJob(Config $jobConfig): string
    {
        return $this->getExecDirForJob($jobConfig)
            . '/' . $this->githubOwner . '-' . $this->githubRepository . '-' . $jobConfig->getCommitHash();
    }

    public function getPipelineFileForJob(Config $jobConfig): string
    {
        return $this->getUnpackedRepoDirForJob($jobConfig) . '/pipeline.json';
    }

    private function buildDestinationPath(Config $jobConfig): string
    {
        return $this->githubOwner
            . '/' . $this->githubRepository
            . '/' . $jobConfig->getBranch()
            . '/' . $jobConfig->getCommitHash()
            . '/' . $jobConfig->getBuildNumber();
    }
}
