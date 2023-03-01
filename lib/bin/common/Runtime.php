<?php

use Symfony\Component\DependencyInjection as DI;
use App\DependencyInjection as CustomDI;

class Runtime
{
    private DI\ContainerInterface $container;

    public function buildAndRun(): void
    {
        $this->buildContainer();
        $this->run();
    }

    private function buildContainer(): void
    {
        $container = $this->container = new DI\ContainerBuilder();
        $container->setParameter('application.root_dir', \Application::getRootDirectory());
        $container->addCompilerPass(new CustomDI\DoctrineCompilerPass());
        $container->registerExtension($extension = new CustomDI\ApplicationExtension());
        $container->loadFromExtension($extension->getAlias());

        $container->compile(true);
    }

    private function run(): void
    {
        $this->container->get(Application::class)->run();
    }
}
