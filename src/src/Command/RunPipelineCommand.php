<?php

namespace App\Command;

use App\Pipeline\PipelineFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('dupa')]
class RunPipelineCommand extends Command
{
    private PipelineFactory $factory;

    /** @required */
    public function setUpFactory(PipelineFactory $factory): void
    {
        $this->factory = $factory;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump($this->factory->create());
        return 0;
    }
}