<?php

namespace App\Github\Request;

use App\Job\Job;

abstract class AbstractStatusRequestCreator extends AbstractGithubRequestCreator
{
    public function supports(RequestType $type, ?object $subject): bool
    {
        return $subject instanceof Job;
    }

    protected function getRequestBody(?object $subject, array $context = []): array
    {
        return [
            'description' => $context['description'] ?? 'Building your commit',
            'context'     => 'continuous-integration/ci-cd',
        ];
    }

    protected function getUri(?object $subject): string
    {
        if (!$subject instanceof Job) {
            throw new \RuntimeException();
        }

        return \strtr(
            'repos/{owner}/{repo}/statuses/{sha}',
            [
                '{owner}' => $this->githubOwner,
                '{repo}'  => $this->githubRepository,
                '{sha}'   => $subject->getCurrentCommit(),
            ]
        );
    }
}
