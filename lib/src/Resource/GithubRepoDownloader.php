<?php

namespace App\Resource;

use App\Github\HttpClient;
use App\Job\Data\Config;

class GithubRepoDownloader
{
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

        $repoContent = $fileResponse->getBody()->getContents();
        \fwrite($tmpFile, $repoContent);
    }
}
