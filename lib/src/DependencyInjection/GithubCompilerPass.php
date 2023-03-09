<?php

namespace App\DependencyInjection;

use App\Github\Request\RequestFactory;
use App\Pipeline\Exception\MissingEnvVariableException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use function Symfony\Component\String\b;

class GithubCompilerPass implements CompilerPassInterface
{
    public const SERVICE_TAG_REQUEST_CREATOR = 'dupa';

    public function process(ContainerBuilder $container): void
    {
        $this->setupRequestFactory($container);
    }

    private function setupRequestFactory(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(RequestFactory::class);

        $definition->addArgument(
            \array_map(
                fn(string $id) => new Reference($id),
                \array_keys($container->findTaggedServiceIds(self::SERVICE_TAG_REQUEST_CREATOR))
            ),
        );

        $container->setDefinition(RequestFactory::class, $definition);
    }
}
