<?php

namespace App\Command;

use App\Github\HttpClient;
use App\Job\JobFactory;
use App\Runner\Consumer;
use App\Runner\Runner;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
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
            try {
                $headers = $message->get('application_headers');
                $headers = $headers->offsetGet('github_data');

                [
                    'commit_hash'  => $commitHash,
                    'branch_name'  => $branch,
                    'build_number' => $buildNumber,
                ] = $headers;
                $job = $this->jobFactory->create($branch, $commitHash, $buildNumber);
                $this->client->initCommitStatus($job);
                $this->runner->run($job);
            } catch (\Throwable $exception) {
            } finally {
                $this->consumer->acknowledge($message);
            }
        };

        $this->consumer->consume($consumeJobMessage);

        return 0;
    }
}
