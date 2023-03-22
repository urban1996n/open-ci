<?php

namespace App\Job\Event;

use App\Github\HttpClient;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

#[AsEventListener(event: 'job.event.error', method: 'onJobError')]
#[AsEventListener(event: 'job.event.created', method: 'onJobCreated')]
#[AsEventListener(event: 'job.event.status_change', method: 'onJobStatusChange')]
class JobEventsSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly HttpClient $client)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            ErrorEvent::class        => 'onJobError',
            CreatedEvent::class      => 'onJobCreated',
            StatusChangeEvent::class => 'onJobStatusChange',
        ];
    }

    public function onJobError(ErrorEvent $event): void
    {
        dd('asd');
        $this->client->markStatusFailure($event->getConfig(), $event->getException()->getMessage());
    }

    public function onJobCreated(CreatedEvent $event): void
    {
        dd('asd');
        $this->client->initCommitStatus($event->getConfig());
    }

    public function onJobStatusChange(StatusChangeEvent $event): void
    {
        dd('asd');
        $this->client->updateCommitStatus($event->getConfig(), $event->getStatus());
    }
}
