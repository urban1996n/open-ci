<?php

namespace App\Common\Event;

use App\Request\JsonToInputBagDecorator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: 'kernel.request', method: 'onKernelRequest')]
class RequestListener
{
    public function __construct(private readonly JsonToInputBagDecorator $decorator)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ((!$content = $request->getContent()) || $request->headers->get('content-type') !== 'application/json') {
            return;
        }

        $inputBag = $this->decorator->decorate($content);

        $request->request = $inputBag;
    }
}
