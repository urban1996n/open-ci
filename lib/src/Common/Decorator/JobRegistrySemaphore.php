<?php

namespace App\Common\Decorator;

use App\Storage\Redis;
use Symfony\Component\Semaphore\Semaphore;
use Symfony\Component\Semaphore\SemaphoreFactory;
use Symfony\Component\Semaphore\Store\RedisStore;

class JobRegistrySemaphore
{
    private const SEMAPHORE_KEY = 'job_registry_semaphore';

    public function __construct(private readonly Redis $redis)
    {
    }

    public function get(): Semaphore
    {
        return (new SemaphoreFactory($this->getRedisStore()))->createSemaphore(self::SEMAPHORE_KEY, 1);
    }

    private function getRedisStore(): RedisStore
    {
        return new RedisStore($this->redis);
    }
}
