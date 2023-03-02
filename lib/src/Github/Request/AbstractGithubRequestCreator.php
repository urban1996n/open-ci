<?php

namespace App\Github\Request;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Utils;
use Psr\Http\Message\RequestInterface;

abstract class AbstractGithubRequestCreator implements RequestCreatorInterface
{
    public function __construct(protected readonly string $owner, protected readonly string $repository)
    {
    }

    abstract protected function getRequestBody(?object $subject): array;

    abstract protected function getUri(?object $subject): string;

    abstract protected function getMethod(?object $subject): string;

    protected function getHeaders(?object $subject): array
    {
        return [];
    }

    public function create(RequestType $type, ?object $subject): RequestInterface
    {
        $requestBody = $this->getRequestBody($subject) ? Utils::jsonEncode($this->getRequestBody($subject)) : null;

        return new Request(
            $this->getMethod($subject),
            $this->getUri($subject),
            $this->getHeaders($subject),
            $requestBody,
        );
    }
}
