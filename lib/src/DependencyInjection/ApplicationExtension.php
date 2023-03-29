<?php

namespace App\DependencyInjection;

use App\Github\Request\RequestCreatorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Dotenv\Dotenv;

class ApplicationExtension implements ExtensionInterface
{
    private FileLocator $fileLocator;

    public function __construct(string $rootDir)
    {
        $this->fileLocator = new FileLocator($rootDir . '/config');
    }

    public function getAlias(): string
    {
        return 'ci_cd';
    }

    public function getXsdValidationBasePath(): string | false
    {
        return false;
    }

    public function getNamespace(): string
    {
        return 'ci_cd';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $this->loadConfiguration($container);
        $this->registerServices($container);
    }

    private function registerServices(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(RequestCreatorInterface::class)
            ->addTag(GithubCompilerPass::SERVICE_TAG_REQUEST_CREATOR);
    }

    private function loadConfiguration(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, $this->fileLocator);
        $loader->load('services.yaml');
    }
}
