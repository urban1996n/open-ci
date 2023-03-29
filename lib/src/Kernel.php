<?php

namespace App;

use App\DependencyInjection\ApplicationExtension;
use App\DependencyInjection\GithubCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\AddEventAliasesPass;
use App\Job\Event as JobEvent;
use App\Job\Event\JobEvents as Alias;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function buildContainer(): ContainerBuilder
    {
        $container = parent::buildContainer();
        $container->addCompilerPass(new GithubCompilerPass());
        $container->addCompilerPass(
            new AddEventAliasesPass([
                JobEvent\ErrorEvent::class        => Alias::JOB_ERROR->value,
                JobEvent\StatusChangeEvent::class => Alias::JOB_STATUS_CHANGE->value,
                JobEvent\CreatedEvent::class      => Alias::JOB_CREATED->value,
            ])
        );
        $container->registerExtension(new ApplicationExtension($this->getProjectDir()));
        $container->loadFromExtension('ci_cd');

        return $container;
    }
}
