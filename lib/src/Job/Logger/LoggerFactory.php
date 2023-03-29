<?php

namespace App\Job\Logger;

use App\Job\Data\Config;
use App\Resource\Locator;

class LoggerFactory
{
    public function __construct(private readonly Locator $locator)
    {
    }

    public function create(Config $config): Logger
    {
        return new Logger($this->locator->locateLogFilePathFor($config));
    }
}
