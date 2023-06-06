<?php

namespace App\Job\Registry;

use App\Store\Redis;
use Symfony\Component\Semaphore\SemaphoreFactory;
use Symfony\Component\Semaphore\SemaphoreInterface;
use Symfony\Component\Semaphore\Store\RedisStore;

class Semaphore
{
    private const SEMAPHORE_KEY = 'job_registry_semaphore';

    public function __construct(private readonly Redis $redis)
    {
    }

    public function get(): SemaphoreInterface
    {
        return (new SemaphoreFactory($this->getRedisStore()))->createSemaphore(self::SEMAPHORE_KEY, 1);
    }

    private function getRedisStore(): RedisStore
    {
        return new RedisStore($this->redis);
    }
}
