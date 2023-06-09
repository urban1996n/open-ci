<?php

namespace App\Pipeline\Data\Component;

use App\Common\Status;

class Script
{
    private string $context;

    private string $binary;

    private string $command;

    /** @var  Argument[] */
    private array $args = [];

    private bool $finished = false;

    private Status|null $status = null;

    public function __construct(string $context, string $binary, string $command)
    {
        $this->context = $context;
        $this->binary  = $binary;
        $this->command = $command;
    }

    public function addArgument(Argument $argument): void
    {
        $this->args[] = $argument;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getBinary(): string
    {
        return $this->binary;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public static function fromArray(array $initializer): Script
    {
        $context = $initializer['context'];
        $binary  = $initializer['binary'];
        $command = $initializer['command'];

        return new self($context, $binary, $command);
    }

    public function setFinished(bool $finished): void
    {
        $this->finished = $finished;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function isFinished(): bool
    {
        return $this->finished;
    }
}
