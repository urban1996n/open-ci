<?php

namespace App\Resource;

use App\Github\HttpClient;
use App\Job\Data\Config;

class GithubRepoDownloader
{
    /** @var resource[] */
    private array $tmpFiles = [];

    public function __construct(
        private readonly HttpClient $client,
        private readonly Locator $locator,
        private readonly JobFileManager $fileManager
    ) {
    }

    public function download(Config $jobConfig): void
    {
        $fileResponse = $this->client->downloadZipArchive($jobConfig);

        $this->fileManager->createTempDirectory($jobConfig);
        if ($fileResponse->getStatusCode() !== 200
            || !$tmpFile = \fopen($this->locator->locateTemporaryRepoArchiveFile($jobConfig), 'w+')
        ) {
            throw new \RuntimeException();
        }

        $repoContent                                 = $fileResponse->getBody()->getContents();
        $this->tmpFiles[$jobConfig->getIdentifier()] = $tmpFile;
        \fwrite($tmpFile, $repoContent);
    }

    public function remove(Config $jobConfig): void
    {
        $tmpFile = $this->tmpFiles[$jobConfig->getIdentifier()] ?? null;
        if (!$tmpFile) {
            throw new \RuntimeException();
        }

        $this->fileManager->removeTempDirectory($jobConfig);
        unset($this->tmpFiles[$jobConfig->getIdentifier()]);
    }
}
