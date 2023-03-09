<?php

namespace App\Job\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as BaseLogger;

class Logger extends BaseLogger
{
    public function __construct(
        private readonly string $rootDir,
        private readonly string $branch,
        private readonly string $commitHash,
        private readonly int $buildNumber
    ) {
        parent::__construct('ci_cd_app');

        $this->pushHandler(new StreamHandler($this->composeStreamPath(), Level::Info));
    }

    private function composeStreamPath(): string
    {
        return $this->rootDir
            . '/'
            . '../var/'
            . $this->branch
            . '/'
            . $this->commitHash
            . '/'
            . $this->buildNumber
            . '.log';
    }
}
