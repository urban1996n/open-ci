<?php

namespace App\Resource;

use App\Github\HttpClient;
use App\Job\Job;

class GithubRepoDownloader
{
    /** @var resource[] */
    private array $tmpFiles = [];

    public function __construct(
        private readonly HttpClient $client,
        private readonly Locator $locator,
        private readonly FileManager $fileManager
    ) {
    }

    public function download(Job $job): void
    {
        $fileResponse = $this->client->downloadZipArchive($job);

        $this->fileManager->createDirectory($this->locator->getTempDirForJob($job));
        if ($fileResponse->getStatusCode() !== 200
            || !$tmpFile = \fopen($this->locator->getTemporaryRepoArchiveFile($job), 'w+')
        ) {
            throw new \RuntimeException();
        }

        $repoContent                           = $fileResponse->getBody()->getContents();
        $this->tmpFiles[$job->getIdentifier()] = $tmpFile;
        \fwrite($tmpFile, $repoContent);
    }

    public function remove(Job $job): void
    {
        $tmpFile = $this->tmpFiles[$job->getIdentifier()] ?? null;
        if (!$tmpFile) {
            throw new \RuntimeException();
        }

        $this->fileManager->removeDir($this->locator->getTempDirForJob($job));
        unset($this->tmpFiles[$job->getIdentifier()]);
    }
}
