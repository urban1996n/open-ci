<?php

namespace App\Resource;

use App\Job\Data\Config;

class JobFileManager extends FileManager
{
    public function __construct(private readonly Locator $locator)
    {
    }

    public function createTempArchiveDirectory(Config $config): void
    {
        $this->createDirectory($this->locator->locateArchiveDirFor($config));
    }

    public function removeTempArchiveDirectory(Config $config): void
    {
        $this->removeDir($this->locator->locateArchiveDirFor($config));
    }

    public function createExecDirectory(Config $config): void
    {
        $this->createDirectory($this->locator->locateExecDirFor($config));
    }

    public function removeUnpackedRepoDirectory(Config $config): void
    {
        $this->removeDir($this->locator->locateExecDirFor($config));
    }
}
