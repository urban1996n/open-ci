<?php

namespace App\Job\Event;

use App\Github\HttpClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: JobEvents::JOB_ERROR, method: 'onJobError')]
#[AsEventListener(event: JobEvents::JOB_CREATED, method: 'onJobCreated')]
#[AsEventListener(event: JobEvents::JOB_STATUS_CHANGE, method: 'onJobStatusChange')]
class JobEventsSubscriber
{
    public function __construct(private readonly HttpClient $client, private readonly LoggerInterface $logger)
    {
    }

    public function onJobError(ErrorEvent $event): void
    {
        $this->logger->error($event->getException()->getMessage());
        $this->client->markStatusFailure($event->getConfig(), $event->getException()->getMessage());
    }

    public function onJobCreated(CreatedEvent $event): void
    {
        $this->client->initCommitStatus($event->getConfig());
    }

    public function onJobStatusChange(StatusChangeEvent $event): void
    {
        $this->client->updateCommitStatus($event->getConfig(), $event->getStatus());
    }
}
