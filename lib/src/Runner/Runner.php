<?php

namespace App\Runner;

use App\Job\Job;
use App\Resource\FileManager;
use App\Resource\GithubArchiveManager;
use App\Resource\GithubRepoDownloader;
use App\Resource\Locator;

class Runner
{
    public function __construct(
        private readonly GithubRepoDownloader $downloader,
        private readonly GithubArchiveManager $archiveManager,
        private readonly FileManager $fileManager,
        private readonly Locator $locator,
    ) {
    }

    public function run(Job $job): void
    {
        $this->runPreExecutionTasks($job);
        $this->runJobExecutionTasks($job);
        $this->runPostExecutionTasks($job);
    }

    private function runPreExecutionTasks(Job $job): void
    {
        $this->downloader->download($job);
        $this->archiveManager->unpack($job);
        $this->downloader->remove($job);
    }

    private function runJobExecutionTasks(Job $job): void
    {
        $job->start();
    }

    private function runPostExecutionTasks(Job $job): void
    {
        $this->fileManager->removeDir($this->locator->getUnpackedRepoDirForJob($job));
    }
}
