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
        $this->validateSubject($subject);

        return [
            'description' => $context['description'] ?? 'Building your commit',
            'context'     => 'continuous-integration/ci-cd',
            'target_url'  => $this->getDetailsUrlFor($subject),
        ];
    }

    protected function getUri(?object $subject): string
    {
        $this->validateSubject($subject);

        return \strtr(
            'repos/{owner}/{repo}/statuses/{sha}',
            [
                '{owner}' => $this->githubOwner,
                '{repo}'  => $this->githubRepository,
                '{sha}'   => $subject->getCommitHash(),
            ]
        );
    }

    private function getDetailsUrlFor(Config $job): string
    {
        return $this->router->generate(
            'build_log_show',
            [
                'branch'      => $job->getBranch(),
                'commitHash'  => $job->getCommitHash(),
                'buildNumber' => $job->getBuildNumber(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    private function validateSubject(?object $subject): void
    {
        if (!$subject instanceof Config) {
            throw RequestCreationException::invalidSubject(
                Config::class, $subject === null ? 'null' : \get_class($subject)
            );
        }
    }
}
