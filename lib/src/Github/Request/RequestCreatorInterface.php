<?php

namespace App\Github\Request;

use Psr\Http\Message\RequestInterface;

interface RequestCreatorInterface
{
    public function supports(RequestType $type, ?object $subject): bool;
    public function create(RequestType $type, ?object $subject): RequestInterface;
}
