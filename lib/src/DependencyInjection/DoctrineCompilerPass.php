<?php

namespace App\DependencyInjection;

use Doctrine\ORM\ORMSetup;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {

    }

    private function setupConfiguration(ContainerBuilder $container): void
    {
        $rootDir = $container->getParameter('application.root_dir');

        $config = ORMSetup::createAttributeMetadataConfiguration(
            [$rootDir . '/src/Entity'],
            true,
            $rootDir . '/var/Proxies'
        );
    }
}