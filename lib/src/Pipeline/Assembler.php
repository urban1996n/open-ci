<?php

namespace App\Pipeline;

use App\Pipeline\Data\Component\Argument;
use App\Pipeline\Data\Component\Environment;
use App\Pipeline\Data\Component\EnvVar;
use App\Pipeline\Data\Component\Script;
use App\Pipeline\Data\Component\Stage;
use App\Pipeline\Data\Component\Step;
use App\Pipeline\Data\Pipeline;

class Assembler
{
    public function assemble(array $initializer): Pipeline
    {
        $pipeline            = new Pipeline();
        $pipelineEnvironment = new Environment();

        foreach ($initializer['environment'] as $envVar) {
            $pipelineEnvironment->addVariable(EnvVar::fromArray($envVar));
        }

        $pipeline->setEnv($pipelineEnvironment);

        foreach ($initializer['preBuild']['scripts'] as $prebuildScript) {
            $script = Script::fromArray($prebuildScript);

            foreach ($prebuildScript['args'] ?? [] as $argument) {
                $script->addArgument(Argument::fromArray($argument));
            }

            $pipeline->addPrebuildScript($script);
        }

        foreach ($initializer['build']['stages'] as $stageInitializer) {
            $stage = Stage::fromArray($stageInitializer);

            $stageEnvironment = new Environment();
            foreach ($stageInitializer['env_vars'] as $envVar) {
                $stageEnvironment->addVariable(EnvVar::fromArray($envVar));
            }

            foreach ($stageInitializer['steps'] as $stepInitializer) {
                $step = new Step($stepInitializer['name']);

                foreach ($stepInitializer['scripts'] as $scriptInitializer) {
                    $script = Script::fromArray($scriptInitializer);

                    foreach ($scriptInitializer['args'] ?? [] as $argument) {
                        $argument = Argument::fromArray($argument);
                        $script->addArgument($argument);
                    }

                    $step->addScript($script);
                }
            }

            $stage->setEnvironment($stageEnvironment);
            $pipeline->addStage($stage);
        }

        return $pipeline;
    }
}
