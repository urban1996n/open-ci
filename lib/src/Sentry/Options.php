<?php

namespace App\Sentry;

use Sentry\Event;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\RequestStack;
use function Sentry\withScope;

class Options
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function onBeforeSend(): \Closure
    {
        return function (Event $event): Event {
            $this->configureScope();

            return $event;
        };
    }

    private function configureScope(): void
    {
        $setContext = function (Scope $scope) {
            $request       = $this->requestStack->getCurrentRequest();
            if (!$request) {
                return;
            }

            $requestParams = $request->getMethod() === 'POST'
                ? $request->request
                : $request->query;

            $scope->setContext('request', $requestParams->all());
        };

        withScope($setContext);
    }
}
