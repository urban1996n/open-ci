<?php

namespace App\Github\Request;

use App\Job\Job;

abstract class AbstractStatusRequestCreator extends AbstractGithubRequestCreator
{
    public function supports(RequestType $type, ?object $subject): bool
    {
        return \in_array($type, [RequestType::COMMIT_STATUS_GET, RequestType::COMMIT_STATUS_UPDATE])
            && $subject instanceof Job;
    }

    protected function getRequestBody(?object $subject): array
    {
        return [
            'state'       => 'pending',
            'target_url'  => 'https://example.com/build/status',
            'description' => 'Building your commit!',
            'context'     => 'continuous-integration/ci-cd',
        ];
    }

    protected function getUri(?object $subject): string
    {
        if (!$subject instanceof Job) {
            return '';
        }

        return \strtr(
            'repos/{owner}/{repo}/statuses/{sha}',
            ['{owner}' => $this->owner, '{repo}' => $this->repository, '{sha}' => $subject->getCurrentCommit()]
        );
    }
}
