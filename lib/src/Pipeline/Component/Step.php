<?php

namespace App\Pipeline\Component;

class Step
{
    private string $name;

    /** @var Script[] */
    private array $scripts;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addScript(Script $script): void
    {
        $this->scripts[] = $script;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScripts(): array
    {
        return $this->scripts;
    }
}