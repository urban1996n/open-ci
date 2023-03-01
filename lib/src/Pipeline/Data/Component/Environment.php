<?php

namespace App\Pipeline\Data\Component;

class Environment
{
    /** @var EnvVar[] */
    private array $variables;

    public function addVariable(EnvVar $envVar): void
    {
        $this->variables[] = $envVar;
    }

    /*** @return EnvVar[] */
    public function getVariables(): array
    {
        return $this->variables;
    }
}