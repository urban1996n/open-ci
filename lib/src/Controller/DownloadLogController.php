<?php

namespace App\Controller;

use App\Job\Data\Config;
use App\Resource\Locator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

class DownloadLogController extends AbstractController
{
    #[Route('/result/download/{branch}-{commitHash}-{buildNumber}', name: 'download_log')]
    public function downloadLog(
        string $branch,
        string $commitHash,
        int $buildNumber,
        Locator $locator,
        Filesystem $filesystem
    ): BinaryFileResponse {
        $path = $locator->locateLogFilePathFor(new Config($branch, $commitHash, $buildNumber));
        if (!$filesystem->exists($path)) {
            throw $this->createNotFoundException('File not found');
        }

        return new BinaryFileResponse($path);
    }

}
