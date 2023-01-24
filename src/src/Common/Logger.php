<?php

namespace App\Common;

use Monolog\Logger as BaseLogger;

class Logger extends BaseLogger
{
    public function __construct()
    {
        parent::__construct('ci_cd_app');
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}