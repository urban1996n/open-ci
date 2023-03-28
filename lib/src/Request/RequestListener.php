<?php

namespace App\Request;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: 'kernel.request', method: 'onKernelRequest')]
class RequestListener
{
    public function __construct(private readonly JsonToInputBagDecorator $decorator)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$content = $request->getContent()) {
            return;
        }

        $inputBag = $this->decorator->decorate($content);

        $request->request = $inputBag;
    }
}
