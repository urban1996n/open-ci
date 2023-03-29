<?php

namespace App\Github\Request;

use App\Job\Data\Config;

class DownloadZipBallRequestCreator extends AbstractGithubRequestCreator
{
    protected function getMethod(?object $subject): string
    {
        return 'GET';
    }

    protected function getRequestBody(?object $subject, array $context = []): array
    {
        return [];
    }

    protected function getHeaders(?object $subject): array
    {
        return ['Accept' => 'application/json'];
    }

    protected function getUri(?object $subject): string
    {
        /** @var Config $subject */

        return \strtr(
            'repos/{owner}/{repo}/zipball/{ref}',
            ['{owner}' => $this->githubOwner, '{repo}' => $this->githubRepository, '{ref}' => $subject->getBranch()]
        );
    }

    public function supports(RequestType $type, ?object $subject): bool
    {
        return $type === RequestType::REPOSITORY_DOWNLOAD && $subject instanceof Config;
    }
}
