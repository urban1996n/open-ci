<?php

namespace App\Pipeline\Data;

use App\Pipeline\Data\Component;

class Pipeline
{
    private Component\Environment $environment;

    /** @var Component\Stage[] */
    private array $stages;

    /** @var Component\Script[] */
    private array $prebuildScripts;

    public function addPrebuildScript(Component\Script $script): void
    {
        $this->prebuildScripts[] = $script;
    }

    public function setEnv(Component\Environment $environment): void
    {
        $this->environment = $environment;
    }

    public function addStage(Component\Stage $stage): void
    {
        $this->stages[] = $stage;
    }

    public function getEnvironment(): Component\Environment
    {
        return $this->environment;
    }

    /** @return Component\Script[] */
    public function getPrebuildScripts(): array
    {
        return $this->prebuildScripts;
    }

    /** @return Component\Stage[] */
    public function getStages(): array
    {
        return $this->stages;
    }
}
