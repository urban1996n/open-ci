<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection as DI;
use App\DependencyInjection as CustomDI;
use Symfony\Component\Dotenv\Dotenv;

class Runtime
{
    private DI\ContainerInterface $container;

    private FileLocator $fileLocator;

    public function __construct()
    {
        $this->container   = new DI\ContainerBuilder();
        $this->fileLocator = new FileLocator(\Application::getRootDirectory());
    }

    public function buildAndRun(): void
    {
        $this->loadEnvironment();
        $this->buildContainer();
        $this->run();
    }

    private function buildContainer(): void
    {
        $container = $this->container;
        $container->setParameter('application.root_dir', \Application::getRootDirectory());
        $container->registerExtension($extension = new CustomDI\ApplicationExtension());
        $container->loadFromExtension($extension->getAlias());
        $container->addCompilerPass(new CustomDI\GithubCompilerPass());

        $container->compile(true);
    }

    private function loadEnvironment(): void
    {
        $dotenv = new Dotenv();
        $dotenv->load($this->fileLocator->locate('config/.env'));
    }

    private function run(): void
    {
        $this->container->get(Application::class)->run();
    }
}
