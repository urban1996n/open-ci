<?php

use Sentry\SentryBundle\SentryBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    MonologBundle::class => ['all' => true],
    SentryBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
];
