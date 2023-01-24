<?php

namespace App\Common;

use Monolog\Logger as BaseLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

class Logger extends BaseLogger
{
    private string $rootDir;

    public function __construct(string $rootDir)
    {
        parent::__construct('ci_cd_app');

        $this->pushHandler(new StreamHandler($rootDir . '/../var/' . $this->name . '.log', Level::Info));
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}