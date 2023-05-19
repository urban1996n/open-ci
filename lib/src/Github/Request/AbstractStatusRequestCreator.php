<?php

namespace App\Github\Request;

use App\Job\Config;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractStatusRequestCreator extends AbstractGithubRequestCreator
{
    private RouterInterface $router;

    #[Required]
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    public function supports(RequestType $type, ?object $subject): bool
    {
        return $subject instanceof Config;
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
        if (!$subject instanceof Config) {
            throw new \RuntimeException();
        }

        return \strtr(
            'repos/{owner}/{repo}/statuses/{sha}',
            [
                '{owner}' => $this->githubOwner,
                '{repo}'  => $this->githubRepository,
                '{sha}'   => $subject->getCommitHash(),
            ]
        );
    }

    protected function getDetailsUrlFor(Config $job): string
    {
        return $this->router->generate(
            'download_log',
            [
                'branch'      => $job->getBranch(),
                'commitHash'  => $job->getCommitHash(),
                'buildNumber' => $job->getBuildNumber(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
