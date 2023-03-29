<?php

namespace App\AMQP\Event;

use App\AMQP\JobMessage;
use App\Http\JobMessenger;
use App\Job\Data\Config;
use App\Storage\Redis;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: AmqpEvents::AMQP_EXCEPTION, method: 'onAmqpException')]
#[AsEventListener(event: AmqpEvents::AMQP_IO_CONNECTION, method: 'onAmqpConnection')]
class AmqpListener
{
    public function __construct(private readonly Redis $redis, private readonly JobMessenger $jobMessenger)
    {
    }

    public function onAmqpException(AmqpExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $commit  = $request->request->get('after');
        $branch  = \explode('/', $request->request->get('ref'));
        $branch  = \end($branch);

        if ($commit && $branch) {
            $findSimilar = fn(array $jobConfig) => $jobConfig['branch'] === $branch && $jobConfig['commit'] === $commit;

            if (!\array_filter($undeliveredJobs = $this->redis->getCachedUndeliveredJobs(), $findSimilar)) {
                $undeliveredJobs[] = ['branch' => $branch, 'commit' => $commit];
            }

            $this->redis->cacheUndeliveredJobs($undeliveredJobs);
        }
    }

    public function onAmqpConnection(): void
    {
        foreach ($this->redis->getCachedUndeliveredJobs() as $jobElement) {
            $this->jobMessenger->send(new JobMessage($jobElement['branch'], $jobElement['commit']));
        }
    }
}
