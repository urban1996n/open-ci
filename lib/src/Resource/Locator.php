<?php

namespace App\Resource;

use App\Job\Config;

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
        return $this->locateTempDir() . '/exec';
    }

    public function locateArchiveDirFor(Config $jobConfig): string
    {
        return $this->locateTempDir() . '/tmp_archive/' . $this->buildDestinationPath($jobConfig);
    }

    public function locateTemporaryRepoArchiveFile(Config $jobConfig): string
    {
        return $this->locateArchiveDirFor($jobConfig) . '/' . $jobConfig->getIdentifier() . '.zip';
    }

    public function locateExecDirFor(Config $jobConfig): string
    {
        return $this->locateExecDir() . '/' . $this->buildDestinationPath($jobConfig);
    }

    public function locateUnpackedRepoDirFor(Config $jobConfig): string
    {
        return $this->locateExecDirFor($jobConfig) . '/' . $this->githubOwner . '-' . $this->githubRepository . '-'
            . $jobConfig->getCommitHash();
    }

    public function locatePipelineFileFor(Config $jobConfig): string
    {
        return $this->locateUnpackedRepoDirFor($jobConfig) . '/pipeline.json';
    }

    public function locateLogFilePathFor(Config $jobConfig): string
    {
        return $this->locateTempDir() . '/logs/' . $this->buildDestinationPath($jobConfig);
    }

    public function locateConfigDir(): string
    {
        return $this->rootDir . '/config';
    }

    public function locateConfigFile(string $fileName): string
    {
        return $this->locateConfigDir() . '/' . $fileName;
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
