<?php

namespace App\Storage;

use \Redis as BaseRedis;

class Redis extends BaseRedis
{
    public function __construct(private readonly string $redisUrl, string $redisPassword)
    {
        parent::__construct();

        $this->connect($this->redisUrl);
        $this->auth($redisPassword);
    }
}
