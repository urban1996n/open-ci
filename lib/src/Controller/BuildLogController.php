<?php

namespace App\Controller;

use App\Job\Data\Config;
use App\Filesystem\Filesystem;
use App\Resource\Locator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/build/log', name: 'build_log')]
class BuildLogController extends AbstractController
{
    #[Route('/{branch}-{commitHash}-{buildNumber}', name: 'download_log', methods: ['GET'])]
    public function fetch(
        string     $branch,
        string     $commitHash,
        int        $buildNumber,
        Locator    $locator,
        Filesystem $filesystem
    ): Response {
        $path = $locator->locateLogFilePathFor(new Config($branch, $commitHash, $buildNumber));
        if (!$filesystem->exists($path)) {
            throw $this->createNotFoundException('File not found');
        }

        return new Response($filesystem->getContents($path));
    }

    #[Route('/show/{branch}-{commitHash}-{buildNumber}')]
    public function show(): Response
    {
        return $this->render('log/show');
    }
}
