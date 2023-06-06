<?php

namespace App\Command;

use App\Job\ActionResolver;
use App\Job\Config;
use App\Runner\Consumer;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand('pipeline:execute')]
class RunPipelineCommand extends Command
{
    private Consumer $consumer;

    private ActionResolver $actionResolver;

    #[Required]
    public function setRunner(ActionResolver $actionResolver): void
    {
        $this->actionResolver = $actionResolver;
    }

    #[Required]
    public function setConsumer(Consumer $consumer): void
    {
        $this->consumer = $consumer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $consumeJobMessage = function (AMQPMessage $message) use ($input, $output): void {
            try {
                $headers = $message->get('application_headers');
                $headers = $headers->offsetGet('github_data');
                [
                    'commit_hash' => $commitHash,
                    'branch_name' => $branch,
                ] = $headers;

                // TODO: Add redis storage: [$branch_$commitHash: buildNumber] and increment on each similar job found.
                // Probably in resolver itself
                $this->actionResolver->resolve(new Config($branch, $commitHash, 1));
            } finally {
                $this->consumer->acknowledge($message);
            }
        };

        $this->consumer->consume($consumeJobMessage);

        return 0;
    }
}
