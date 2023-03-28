<?php

namespace App\Job\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as BaseLogger;

class Logger extends BaseLogger
{
    public function __construct(string $streamSource) {
        parent::__construct('ci_cd_app');

        $this->pushHandler(new StreamHandler($streamSource, Level::Info));
    }
}
