<?php

namespace App\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Dotenv\Dotenv;

class ApplicationExtension implements ExtensionInterface
{
    private FileLocator $fileLocator;

    public function __construct()
    {
        $this->fileLocator = new FileLocator(__DIR__ . '/../../config');
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
        $this->loadEnvironment();
    }

    private function buildContainer(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, $this->fileLocator);
        $loader->load('services.yaml');
    }

    private function loadEnvironment(): void
    {
        $dotenv = new Dotenv();
        $dotenv->load($this->fileLocator->locate('.env'));
    }
}
