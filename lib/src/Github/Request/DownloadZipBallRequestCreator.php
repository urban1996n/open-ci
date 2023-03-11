<?php

namespace App\Github\Request;

use App\Job\Job;

class DownloadZipBallRequestCreator extends AbstractGithubRequestCreator
{
    protected function getMethod(?object $subject): string
    {
        return 'GET';
    }

    protected function getRequestBody(?object $subject): array
    {
        return [];
    }

    protected function getHeaders(?object $subject): array
    {
        return ['Accept' => 'application/json'];
    }

    protected function getUri(?object $subject): string
    {
        /** @var Job $subject */

        return \strtr(
            'repos/{owner}/{repo}/zipball/{ref}',
            ['{owner}' => $this->githubOwner, '{repo}' => $this->githubRepository, '{ref}' => $subject->getBranch()]
        );
    }

    public function supports(RequestType $type, ?object $subject): bool
    {
        return $type === RequestType::REPOSITORY_DOWNLOAD && $subject instanceof Job && !$subject->isFinished();
    }
}