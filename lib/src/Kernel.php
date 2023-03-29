<?php

namespace App;

use App\DependencyInjection\ApplicationExtension;
use App\DependencyInjection\GithubCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\AddEventAliasesPass;
use App\Job\Event as JobEvent;
use App\Job\Event\JobEvents as JobAlias;
use App\AMQP\Event as AmqpEvent;
use App\AMQP\Event\AmqpEvents as AmqpAlias;
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
                JobEvent\ErrorEvent::class             => JobAlias::JOB_ERROR,
                JobEvent\StatusChangeEvent::class      => JobAlias::JOB_STATUS_CHANGE,
                JobEvent\CreatedEvent::class           => JobAlias::JOB_CREATED,
                AmqpEvent\AmqpIOConnectionEvent::class => AmqpAlias::AMQP_IO_CONNECTION,
                AmqpEvent\AmqpExceptionEvent::class    => AmqpAlias::AMQP_EXCEPTION,
            ])
        );
        $container->registerExtension(new ApplicationExtension($this->getProjectDir()));
        $container->loadFromExtension('ci_cd');

        return $container;
    }
}
