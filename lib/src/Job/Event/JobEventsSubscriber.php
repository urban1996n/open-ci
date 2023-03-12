<?php

namespace App\Job\Event;

use App\Github\HttpClient;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'job.event.error', method: 'onJobError')]
#[AsEventListener(event: 'job.event.created', method: 'onJobCreated')]
#[AsEventListener(event: 'job.event.status_change', method: 'onJobStatusChange')]
class JobEventsSubscriber
{
    public function __construct(private readonly HttpClient $client)
    {
    }

    public function onJobError(ErrorEvent $event): void
    {
        $this->client->markStatusFailure($event->getJob(), $event->getException()->getMessage());
    }

    public function onJobCreated(CreatedEvent $event): void
    {
        $this->client->initCommitStatus($event->getJob());
    }

    public function onJobStatusChange(StatusChangeEvent $event): void
    {
        $this->client->updateCommitStatus($event->getJob());
    }
}
