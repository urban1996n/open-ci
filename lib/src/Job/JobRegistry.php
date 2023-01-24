<?php

namespace App\Job;

use App\Job\Data\Job;

class JobRegistry
{
    public const JOB = 'job';

    /** @var Job[] */
    private static array $jobs= [];

    private static PipelineExecutor $pipelineExecutor;

    public function __construct(PipelineExecutor $pipelineExecutor)
    {
        static::$pipelineExecutor = $pipelineExecutor;
    }

    /** @param string $key - a current commit hash*/
    final public static function add(string $key)
    {
        static::$jobs[$key] = new Job($key, static::$pipelineExecutor);
    }

    /** @param string $key - a current commit hash*/
    final public static function get(string $key): Job
    {
        return static::$jobs[$key];
    }
}