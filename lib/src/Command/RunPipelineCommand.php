<?php

namespace App\Command;

use App\Job\ActionResolver;
use App\Job\Data\Config;
use App\Job\Event\CreatedEvent;
use App\Job\Event\ErrorEvent;
use App\Job\Event\JobEvents;
use App\Runner\Consumer;
use http\Exception\RuntimeException;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand('pipeline:run-single')]
class RunPipelineCommand extends Command
{
    private Consumer $consumer;

    private ActionResolver $actionResolver;

    private $dispatcher;

    #[Required]
    public function setRunner(
        ActionResolver $actionResolver,
        Consumer $consumer,
        EventDispatcherInterface $dispatcher
    ): void {
        $this->actionResolver = $actionResolver;
        $this->consumer       = $consumer;
        $this->dispatcher     = $dispatcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $buildNumber = 0;

        $consumeJobMessage = function (AMQPMessage $message) use ($input, $output, &$buildNumber): void {
            try {
                $headers = $message->get('application_headers');
                $headers = $headers->offsetGet('github_data');

                [
                    'commit_hash' => $commitHash,
                    'branch_name' => $branch,
                ] = $headers;

                $this->actionResolver->resolve(new Config($branch, $commitHash, $buildNumber++));
            } finally {
                $this->consumer->acknowledge($message);
            }
        };

        $this->consumer->consume($consumeJobMessage);

        return 0;
    }
}
