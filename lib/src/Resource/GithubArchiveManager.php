<?php

namespace App\Resource;

use App\Job\Data\Config;

class GithubArchiveManager
{
    private \ZipArchive $archive;

    public function __construct(
        private readonly Locator $locator,
        private readonly FileManager $fileManager
    ) {
        $this->archive = new \ZipArchive();
    }

    public function unpack(Config $jobConfig): void
    {
        if ($this->archive->open($this->locator->locateTemporaryRepoArchiveFile($jobConfig)) === true) {
            $this->fileManager->createDirectory($jobExecDir = $this->locator->renameExecDirFor($jobConfig));
            $this->archive->extractTo($jobExecDir);
            $this->archive->close();
        } else {
            throw new \RuntimeException();
        }
    }
}
