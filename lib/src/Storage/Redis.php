<?php

namespace App\Storage;

use Redis as BaseRedis;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class Redis extends BaseRedis
{
    private const CACHE_KEY_UNDELIVERED_TAGS = 'redis.undelivered.tags';
    private const CACHE_KEY_JOB_REGISTRY     = 'job.registry.jobs_queue';

    private SerializerInterface $serializer;

    public function __construct(string $redisUrl, string $redisPassword)
    {
        parent::__construct();

        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->connect($redisUrl);
        $this->auth($redisPassword);
    }

    public function cacheJobsRegistry(array $jobs): void
    {
        $this->cacheArray(self::CACHE_KEY_JOB_REGISTRY, $jobs);
    }

    public function getCachedJobsRegistry(): array
    {
        return $this->getCachedArray(self::CACHE_KEY_JOB_REGISTRY);
    }


    public function cacheUndeliveredJobs(array $undeliveredJobs): void
    {
        $this->cacheArray(self::CACHE_KEY_UNDELIVERED_TAGS, $undeliveredJobs);
    }

    public function getCachedUndeliveredJobs(): array
    {
        return $this->getCachedArray(self::CACHE_KEY_UNDELIVERED_TAGS);
    }

    private function getCachedArray(string $cacheKey): array
    {
        return $this->get($cacheKey)
            ? $this->serializer->decode($this->get($cacheKey), JsonEncoder::FORMAT)
            : [];
    }

    private function cacheArray(string $cacheKey, array $array): void
    {
        $this->set($cacheKey, $this->serializer->serialize($array, JsonEncoder::FORMAT));
    }
}
