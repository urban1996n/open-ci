<?php

namespace App\Github\Request;

class RequestCreationException extends RequestException
{
    public static function invalidRequestCreator(string $message = 'Invalid request creator.'): self
    {
        return new self($message);
    }

    public static function invalidRequestType(): self
    {
        return new self('Invalid request type.');
    }

    public static function invalidSubject(string $expected, string $provided): self
    {
        return new self('Expected subject to be instance of ' . $expected . 'but got ' . $provided . ' instead.');
    }
}
