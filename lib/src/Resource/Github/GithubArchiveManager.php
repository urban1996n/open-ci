<?php

namespace App\Resource\Github;

use App\Job\Config;
use App\Resource\JobFileManager;
use App\Resource\Locator;

class GithubArchiveManager
{
    private \ZipArchive $archive;

    public function __construct(
        private readonly Locator $locator,
        private readonly JobFileManager $fileManager
    ) {
        $this->archive = new \ZipArchive();
    }

    public function unpack(Config $jobConfig): void
    {
        if ($this->archive->open($this->locator->locateTemporaryRepoArchiveFile($jobConfig)) === true) {
            $this->fileManager->createExecDirectory($jobConfig);
            $this->archive->extractTo($this->locator->locateExecDirFor($jobConfig));
            $this->archive->close();
        } else {
            throw new \RuntimeException('Could not open archive file.');
        }
    }
}
