<?php

namespace App\Command;

use App\Github\HttpClient;
use App\Job\Job;
use App\Job\JobFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('pipeline:run-single')]
class RunPipelineCommand extends Command
{
    private HttpClient $client;

    private JobFactory $factory;

    /** @required */
    public function setUpFactory(HttpClient $client, JobFactory $factory): void
    {
        $this->client  = $client;
        $this->factory = $factory;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = $this->factory->create('main2', 'e871d16b5abc6326caa5cce72cbdab7bb13350dd', 1);

        $response = $this->client->createStatusCheck($job);
        var_dump($response->getBody()->getContents());

        return 0;
    }
}
