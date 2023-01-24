<?php

require_once \dirname(__DIR__).'/../vendor/autoload_runtime.php';

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct(iterable $commands = [])
    {
        parent::__construct('ci_cd_client', 0.01);

        foreach ($commands as $command) {
            $this->add($command);
        }
    }

    public static function getRootDirectory(): string
    {
        return \dirname(__DIR__);
    }
}