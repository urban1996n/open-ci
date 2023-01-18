<?php

use Symfony\Component\DependencyInjection as DI;
use App\DependencyInjection as CustomDI;

class Runtime
{
    private DI\ContainerInterface $container;

    public function loadAndRun(): void
    {
        $this->buildContainer();
        $this->run();
    }

    private function buildContainer(): void
    {
        $container = $this->container = new DI\ContainerBuilder();
        $container->registerExtension($extension = new CustomDI\ApplicationExtension());
        $container->loadFromExtension($extension->getAlias());
        $container->addCompilerPass(new CustomDI\ApplicationCompilerPass());

        $container->compile(true);
    }

    private function run(): void
    {
        $this->container->get(Application::class)->run();
    }
}
