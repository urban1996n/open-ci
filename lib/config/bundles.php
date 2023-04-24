<?php

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Sentry\SentryBundle\SentryBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    MonologBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true],
    SentryBundle::class => ['all' => true],
];
