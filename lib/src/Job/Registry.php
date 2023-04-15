<?php

namespace App\Job;

use App\Common\Decorator\JobRegistrySemaphore;
use App\Job\Data\Config;
use App\Storage\Redis;
use Ds\Queue;
use Symfony\Component\Semaphore\Semaphore;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Registry
{
    /** @psalm-var Queue<int, Config>[] */
    private array $jobs = [];

    private ?Config $currentJob = null;

    private Semaphore $semaphore;

    public function __construct(private readonly Redis $redis, JobRegistrySemaphore $semaphore)
    {
        $this->semaphore = $semaphore->get();
    }

    public function tryAdd(Config $jobConfig): void
    {
        $do = function () use ($jobConfig) {
            if ($this->inQueue($jobConfig)) {
                return;
            }

            if (!$this->has($jobConfig)) {
                $this->jobs[$jobConfig->getBranch()] = new Queue();
            }

            $queue = $this->jobs[$jobConfig->getBranch()];
            $queue->push($jobConfig);
        };

        $this->do($do);
    }

    public function has(Config $jobConfig): bool
    {
        $do = function () use ($jobConfig) {
            return ($this->jobs[$jobConfig->getBranch()] ?? null) instanceof Queue;
        };

        return $this->do($do);
    }

    public function inQueue(Config $jobConfig): bool
    {
        $do = function () use ($jobConfig) {
            if (!$this->has($jobConfig)) {
                return false;
            }

            $call = function (Config $job) use ($jobConfig) {
                return $job->getIdentifier() === $jobConfig->getIdentifier();
            };

            return \count(
                \array_filter(
                    $this->jobs[$jobConfig->getBranch()]->toArray(),
                    $call
                )
            );
        };

        return $this->do($do);
    }

    public function next(Config $jobConfig): ?Config
    {
        $do = function () use ($jobConfig) {
            $queue = $this->jobs[$jobConfig->getBranch()] ?? null;
            if (!$queue instanceof Queue || $queue->isEmpty()) {
                return null;
            }

            $next = $queue->peek();
            if ($queue->isEmpty()) {
                unset($this->jobs[$jobConfig->getBranch()]);
            }

            if (!$this->currentJob) {
                $this->currentJob = $next;
            }

            return $next;
        };

        return $this->do($do);
    }

    private function do(\Closure $do): mixed
    {
        $result = null;

        if ($this->semaphore->acquire()) {
            $this->load();
            $result = $do();
            $this->dump();
            $this->semaphore->release();
        }

        return $result;
    }

    private function dump(): void
    {
        if (!$this->jobs) {
            return;
        }

        $jobs = $this->jobs;

        $this->redis->cacheJobsRegistry($jobs);
        $this->load();
    }

    private function load(): void
    {
        if (!$queues = $this->redis->getCachedJobsRegistry()) {
            return;
        }

        foreach ($queues as $branch => $configs) {
            $this->jobs[$branch] = new Queue();
            $this->jobs[$branch]->allocate(10);

            \array_map(
                function ($config) use ($branch) {
                    $this->jobs[$branch]->push(
                        new Config($config['branch'], $config['commitHash'], $config['buildNumber'])
                    );
                },
                $configs
            );
        }
    }

    private function getQueue(Config $config): ?Queue
    {
        if ($this->has($config)) {
            return $this->jobs[$config->getBranch()];
        }

        return null;
    }

    public function release(Config $config): void
    {
        $do = function () use ($config) {
            $queue = $this->getQueue($config);
            if ($queue && !$queue->isEmpty()) {
                $queue->pop();
            }

            $this->currentJob = null;
        };

        $this->do($do);
    }

    public function isLocked(): bool
    {
        return !$this->semaphore->acquire();
    }

    public function getCurrentJob(): ?Config
    {
        return $this->currentJob;
    }
}
