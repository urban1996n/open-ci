<?php

namespace App\Common\Event;

use App\Storage\Redis;
use PhpAmqpLib\Exception\AMQPIOException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(event: 'kernel.exception', method: 'onAMQPIOException')]
class AmqpExceptionListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Redis $redis
    ) {
    }

    public function onAMQPIOException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof AMQPIOException) {
            return;
        }

        $this->logger->error($exception->getMessage());
        $request = $event->getRequest();
        $commit  = $request->request->get('after');
        $branch  = \explode('/', $request->request->get('ref'));
        $branch  = \end($branch);

        if ($commit && $branch) {
            $undeliveredJobs = $this->redis->get(Redis::REDIS_KEY_UNDELIVERED_TAGS)
                ? \json_decode($this->redis->get(Redis::REDIS_KEY_UNDELIVERED_TAGS), true)
                : [];

            $findSimilar = fn(array $jobConfig) => $jobConfig['branch'] === $branch && $jobConfig['commit'] === $commit;

            if (!\array_filter($undeliveredJobs, $findSimilar)) {
                $undeliveredJobs[] = ['branch' => $branch, 'commit' => $commit];
            }

            $this->redis->set(Redis::REDIS_KEY_UNDELIVERED_TAGS, \json_encode($undeliveredJobs));
        }
    }
}
