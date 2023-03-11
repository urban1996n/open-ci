<?php

namespace App\Command;

use App\Github\HttpClient;
use App\Job\JobFactory;
use App\Runner\Consumer;
use App\Runner\Runner;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand('pipeline:run-single')]
class RunPipelineCommand extends Command
{
    private Runner $runner;

    private JobFactory $jobFactory;

    private Consumer $consumer;

    private HttpClient $client;

    #[Required]
    public function setRunner(Runner $runner, JobFactory $jobFactory, Consumer $consumer, HttpClient $client): void
    {
        $this->runner     = $runner;
        $this->jobFactory = $jobFactory;
        $this->consumer   = $consumer;
        $this->client     = $client;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $consumeJobMessage = function (AMQPMessage $message): void {
            $headers = $message->get('application_headers');
            ['commit_hash' => $commitHash, 'branch' => $branch, 'build_number' => $buildNumber] = $headers;
            $job = $this->jobFactory->create($branch, $commitHash, $buildNumber);
            $this->client->initCommitStatus($job);
        };

        $this->consumer->consume($consumeJobMessage);
        $job = $this->jobFactory->create('main', '8b5187caa98c9065af9ad9fdece70692fb3552c1', 1);
        $this->runner->run($job);

        return 0;
    }
}
