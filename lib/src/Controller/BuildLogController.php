<?php

namespace App\Controller;

use App\Filesystem\Filesystem;
use App\Job\Config;
use App\Resource\Locator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/build/log', name: 'build_log_')]
class BuildLogController extends AbstractController
{
    #[Route('/{branch}/{commitHash}/{buildNumber}', name: 'download', methods: ['GET'])]
    public function fetch(Config $config, Locator $locator, Filesystem $filesystem): Response
    {
        $path = $locator->locateLogFilePathFor($config);
        if (!$filesystem->exists($path)) {
            throw $this->createNotFoundException('File not found');
        }

        return new Response(nl2br($filesystem->getContents($path)));
    }

    #[Route('/show/{branch}/{commitHash}/{buildNumber}', name: 'show')]
    public function show(Config $config): Response
    {
        return $this->render('log/show.html.twig', ['build' => $config]);
    }
}
