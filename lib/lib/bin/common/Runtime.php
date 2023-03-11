<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection as DI;
use App\DependencyInjection as CustomDI;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel;

class Runtime extends Kernel
{
    protected $container;

    private FileLocator $fileLocator;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, false);

        $this->fileLocator = new FileLocator(\Application::getRootDirectory());
    }

    public function registerBundles(): iterable
    {
        return [Sy];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {

    }

//    protected function buildContainer(): DI\ContainerBuilder
//    {
//        $this->loadEnvironment();
//
//        $this->getContainerBuilder()->registerExtension(new CustomDI\ApplicationExtension());
//        $this->getContainerBuilder()->addCompilerPass(new CustomDI\GithubCompilerPass());
//
//        var_dump($this->getContainerBuilder()->getDefinitions());
//        return parent::buildContainer();
//    }
//
//    private function loadEnvironment(): void
//    {
//        $dotenv = new Dotenv();
//        $dotenv->load($this->fileLocator->locate('config/.env'));
//    }
}
