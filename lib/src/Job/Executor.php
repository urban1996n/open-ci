<?php

namespace App\Job;

use App\Common\Status;
use App\Pipeline\Data\Component\Environment;
use App\Pipeline\Data\Component\Script;
use App\Pipeline\Data\Component\Stage;
use App\Pipeline\Data\Component\Step;
use App\Pipeline\Data\Pipeline;
use App\Pipeline\PipelineFactory;
use Symfony\Component\Dotenv\Dotenv;

class Executor
{
    private ?Pipeline $pipeline = null;

    private \Closure $populateEnvironment;

    private Status $status = Status::Pending;

    public function __construct(private readonly PipelineFactory $factory, private readonly ScriptRunner $scriptRunner)
    {
        $this->populateEnvironment = function (?Environment $environment) {
            if (!$environment) {
                return [];
            }

            $vars = [];
            foreach ($environment->getVariables() as $varName => $varValue) {
                $vars[$varName] = $varValue;
            }

            return $vars;
        };
    }

    public function execute(): void
    {
        $this->pipeline = $this->factory->create();

        $this->loadEnv();
        $this->executePreBuildScripts();
        $this->executeBuild();

        if ($this->status === Status::Pending) {
            $this->status = Status::Success;
        }
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    private function loadEnv(): void
    {
        $dotEnv = new Dotenv();
        $dotEnv->populate($this->populateEnvironment->call($this, $this->pipeline->getEnvironment()));
    }

    private function executePreBuildScripts(): void
    {
        foreach ($this->pipeline->getPrebuildScripts() as $prebuildScript) {
            while ($this->shouldExecute($prebuildScript)) {
                $this->executeScript($prebuildScript, null);
            }
        }
    }

    private function executeBuild(): void
    {
        foreach ($this->pipeline->getStages() as $stage) {
            $this->executeStage($stage);
        }
    }

    private function executeStage(Stage $stage): void
    {
        foreach ($stage->getSteps() as $step) {
            $this->executeStep($step, $stage->getEnvironment());
        }
    }

    private function executeStep(Step $step, Environment $envVars): void
    {
        foreach ($step->getScripts() as $script) {
            while ($this->shouldExecute($script)) {
                $this->executeScript($script, $envVars);
            }
        }
    }

    private function executeScript(Script $script, ?Environment $envVars): void
    {
        $this->scriptRunner->run($script, $this->populateEnvironment->call($this, $envVars));

        if ($script->getSuccessful() === false) {
            $this->status = Status::Failure;
        }
    }

    private function shouldExecute(Script $script): bool
    {
        return $script->getSuccessful() !== false
            && !$this->scriptRunner->isRunning()
            && !$script->isFinished()
            && $this->status === Status::Pending;
    }
}
