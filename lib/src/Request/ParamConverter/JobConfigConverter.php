<?php

namespace App\Request\ParamConverter;

use App\Job\Config;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class JobConfigConverter implements ParamConverterInterface
{
    public function __construct(private ConverterOptions $configuration)
    {
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $request->attributes->set($configuration->getName(), $this->getConfig($request, $configuration->getOptions()));
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === Config::class;
    }

    private function getConfig(Request $request, array $options = []): Config
    {
        $resolvedRequestKeys = $this->configuration->resolve($options);

        $commit = $request->attributes->get($resolvedRequestKeys[ConverterOption::COMMIT_HASH->value] ?? null);
        $branch = $request->attributes->get($resolvedRequestKeys[ConverterOption::BRANCH->value] ?? null);
        $buildNumber = $request->attributes->get($resolvedRequestKeys[ConverterOption::BUILD_NUMBER->value] ?? null);

        if (!$commit || !$branch || !$buildNumber) {
            throw new \RuntimeException('42751800-326e-4299-9400-e23cf446ee69');
        }

        return new Config($branch, $commit, $buildNumber);
    }
}
