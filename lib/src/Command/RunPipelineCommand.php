<?php

namespace App\Command;

use App\Job\Data\Job;
use App\Job\PipelineExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('pipeline:run-single')]
class RunPipelineCommand extends Command
{
    private PipelineExecutor $pipelineExecutor;

    /** @required */
    public function setUpFactory(PipelineExecutor $executor): void
    {
        $this->pipelineExecutor = $executor;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = new Job('test-pipeline-branch', 'abc12456');

        $this->pipelineExecutor->execute($job);

        return 0;
    }
}