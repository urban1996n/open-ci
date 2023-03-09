<?php

namespace App\Github\Request;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class RequestFactory
{
    /** @param RequestCreatorInterface[] $requestCreators */
    public function __construct(private readonly array $requestCreators)
    {
    }

    public function create(RequestType $type, ?object $subject): ?RequestInterface
    {
        foreach ($this->requestCreators as $requestCreator) {
            if ($requestCreator->supports($type, $subject)) {
                return $requestCreator->create($type, $subject);
            }
        }

        return null;
    }
}
