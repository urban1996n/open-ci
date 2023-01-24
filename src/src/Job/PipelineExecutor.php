<?php

namespace App\Job;

use App\Pipeline\PipelineFactory;
use App\Pipeline\Pipeline;
use Symfony\Component\Dotenv\Dotenv;

class PipelineExecutor
{
    private ?Pipeline $pipeline = null;

    private PipelineFactory $factory;

    private ScriptRunner $scriptRunner;

    public function __construct(PipelineFactory $factory, ScriptRunner $scriptRunner)
    {
        $this->factory      = $factory;
        $this->scriptRunner = $scriptRunner;
    }

    public function execute(): void
    {
        $this->pipeline = $this->factory->create();

        $this->loadEnv();
        $this->runPreBuildScripts();
    }

    private function loadEnv(): void
    {
        $dotEnv = new Dotenv();
        foreach ($this->pipeline->getEnvironment()->getVariables() as $envVar) {
            $dotEnv->populate([$envVar->getName() => $envVar->getValue()]);
        }
    }

    private function runPreBuildScripts(): void
    {
        foreach ($this->pipeline->getPrebuildScripts() as $prebuildScript) {
            while (!$this->scriptRunner->isLocked() && !$prebuildScript->isFinished()) {
                $this->scriptRunner->run($prebuildScript);
            }
        }
    }
}