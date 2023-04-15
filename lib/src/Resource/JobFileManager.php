<?php

namespace App\Resource;

use App\Job\Data\Config;

class JobFileManager extends FileManager
{
    public function __construct(private readonly Locator $locator)
    {
    }

    public function createTempDirectory(Config $config): void
    {
        $this->createDirectory($this->locator->locateTempDirFor($config));
    }

    public function removeTempDirectory(Config $config): void
    {
        $this->removeDir($this->locator->locateTempDirFor($config));
    }

    public function createExecDirectory(Config $config): void
    {
        $this->createDirectory($this->locator->locateExecDirFor($config));
    }

    public function removeUnpackedRepoDirectory(Config $config): void
    {
        $this->removeDir($this->locator->locateUnpackedRepoDirFor($config));
    }
}
