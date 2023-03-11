<?php

namespace App\Resource;

use App\Job\Job;

class GithubArchiveManager
{
    private \ZipArchive $archive;

    public function __construct(
        private readonly Locator $locator,
        private readonly FileManager $fileManager
    ) {
        $this->archive = new \ZipArchive();
    }

    public function unpack(Job $job): void
    {
        if ($this->archive->open($this->locator->getTemporaryRepoArchiveFile($job)) === true) {
            $this->fileManager->createDirectory($jobExecDir = $this->locator->getExecDirForJob($job));
            $this->archive->extractTo($jobExecDir);
            $this->archive->close();
        } else {
            throw new \RuntimeException();
        }
    }
}
