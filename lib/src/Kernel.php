<?php

namespace App;

use App\DependencyInjection\ApplicationExtension;
use App\DependencyInjection\GithubCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function buildContainer(): ContainerBuilder
    {
        $container = parent::buildContainer();
        $container->addCompilerPass(new GithubCompilerPass());
        $container->registerExtension(new ApplicationExtension($this->getProjectDir()));
        $container->loadFromExtension('ci_cd');
        return $container;
    }
}
