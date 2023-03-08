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

    public function toArray(): array
    {
        $vars = [];

        foreach ($this->getVariables() as $envVar) {
            $vars[$envVar->getName()] = $envVar->getValue();
        }

        return $vars;
    }
}
