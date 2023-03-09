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

    public function __construct()
    {
        $this->fileLocator = new FileLocator(\Application::getRootDirectory() . '/config');
    }

    public function getAlias()
    {
        return 'ci_cd';
    }

    public function getXsdValidationBasePath()
    {
        return false;
    }

    public function getNamespace()
    {
        return false;
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $this->buildContainer($container);
        $this->registerServices($container);
    }

    private function registerServices(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(RequestCreatorInterface::class)
            ->addTag(GithubCompilerPass::SERVICE_TAG_REQUEST_CREATOR);
    }

    private function buildContainer(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, $this->fileLocator);
        $loader->load('services.yaml');
    }
}
