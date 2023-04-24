<?php

namespace App\Resource;

use App\Job\Data\Config;
use Symfony\Component\Filesystem\Filesystem;

class JobFileManager
{
    public function __construct(private readonly Locator $locator, private readonly Filesystem $filesystem)
    {
    }

    public function createTempArchiveDirectory(Config $config): void
    {
        $this->filesystem->mkdir($this->locator->locateArchiveDirFor($config));
    }

    public function removeTempArchiveDirectory(Config $config): void
    {
        $this->filesystem->remove($this->locator->locateArchiveDirFor($config));
    }

    public function createExecDirectory(Config $config): void
    {
        $this->filesystem->mkdir($this->locator->locateExecDirFor($config));
    }

    public function removeUnpackedRepoDirectory(Config $config): void
    {
        $this->filesystem->remove($this->locator->locateExecDirFor($config));
    }
}
