<?php

namespace App\Pipeline\Data\Component;

class Stage
{
    /** @var Step[] */
    private array $steps;

    private string $name;

    private Environment $environment;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    public function setEnvironment(Environment $environment): void
    {
        $this->environment = $environment;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function addStep(Step $step): void
    {
        $this->steps[] = $step;
    }

    public static function fromArray(array $initializer): Stage
    {
        $name = $initializer['name'];
        $env  = $initializer['env_vars'];

        return new self($name, $env);
    }
}