<?php

namespace App\Job;

use App\Common\Status;
use App\Pipeline\Data\Component\Environment;
use App\Pipeline\Data\Component\Script;
use App\Pipeline\Data\Component\Stage;
use App\Pipeline\Data\Component\Step;
use App\Pipeline\Data\Pipeline;
use Symfony\Component\Dotenv\Dotenv;

class Executor
{
    private ?Pipeline $pipeline = null;

    private Status $status = Status::Pending;

    private ?\Closure $logger = null;

    private ?\Closure $onStatusChange;

    public function __construct(private readonly ScriptRunner $scriptRunner)
    {
    }

    public function execute(Status $status, Pipeline $pipeline, \Closure $logger, \Closure $onStatusChange): void
    {
        $this->status         = $status;
        $this->pipeline       = $pipeline;
        $this->logger         = $logger;
        $this->onStatusChange = $onStatusChange;

        $this->loadEnv();
        $this->executePreBuildScripts();
        $this->executeBuild();

        if ($this->status === Status::InProgress) {
            $this->changeStatus(Status::Success);
        }
    }

    private function loadEnv(): void
    {
        $dotEnv = new Dotenv();
        $dotEnv->populate($this->pipeline->getEnvironment()->toArray());
    }

    private function executePreBuildScripts(): void
    {
        $this->status = Status::InProgress;

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
        $this->scriptRunner->run($script, $envVars?->toArray() ?? [], $this->logger);

        if ($script->getStatus() === Status::Failure) {
            $this->changeStatus(Status::Failure);
        }
    }

    private function shouldExecute(Script $script): bool
    {
        return $script->getStatus() !== Status::Failure
            && !$this->scriptRunner->isRunning()
            && !$script->isFinished()
            && $this->status === Status::InProgress;
    }

    private function changeStatus(Status $status): void
    {
        $onStatusChange = $this->onStatusChange;
        $this->status   = $status;
        $onStatusChange($status);
    }
}
