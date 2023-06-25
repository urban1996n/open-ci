<?php

namespace App\Runner;

use App\Job\Job;
use App\Resource\FileManager;
use App\Resource\Github\GithubArchiveManager;
use App\Resource\Github\GithubRepoDownloader;
use App\Resource\JobFileManager;
use Psr\Log\LoggerInterface;

class Runner
{
    public function __construct(
        private readonly GithubRepoDownloader $downloader,
        private readonly GithubArchiveManager $archiveManager,
        private readonly JobFileManager $fileManager,
        private readonly LoggerInterface $logger
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
        try {
            $this->downloader->download($job->getConfig());
            $this->archiveManager->unpack($job->getConfig());
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        } finally {
            $this->fileManager->removeTempArchiveDirectory($job->getConfig());
        }
    }

    private function runJobExecutionTasks(Job $job): void
    {
        $job->start();
    }

    private function runPostExecutionTasks(Job $job): void
    {
        $this->fileManager->removeUnpackedRepoDirectory($job->getConfig());
    }
}
