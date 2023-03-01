<?php

namespace App\Command;

use App\AMQP\Connection;
use App\Job\Executor;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('pipeline:run-single')]
class RunPipelineCommand extends Command
{
    private Executor $pipelineExecutor;

    private Connection $connection;

    /** @required */
    public function setUpFactory(Executor $executor, Connection $connection): void
    {
        $this->connection       = $connection;
        $this->pipelineExecutor = $executor;
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption('msg', 'm', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channel = $this->connection->getCurrentChannel();

        $channel->queue_declare('hello', false, false, false, false);
        while ($channel->is_open()) {
            if ($input->getOption('msg')) {
                $context = [
                    'commit'       => 'asdasdads',
                    'callback_url' => 'https://github.com/dupa/asdasd',
                ];

                $message = new AMQPMessage(\json_encode($context));
                $this->connection->getCurrentChannel()->basic_publish($message, '', 'hello');
                $this->connection->getCurrentChannel()->close();
                $this->connection->close();
            } else {
                var_dump('asd');
                $callback = function (AMQPMessage $msg) {
                    echo " [x] Done\n";
                };

                $this->connection->getCurrentChannel()->basic_consume(
                    'hello',
                    '',
                    false,
                    true,
                    false,
                    false,
                    $callback
                );

                while ($this->connection->getCurrentChannel()->is_open()) {
                    $this->connection->getCurrentChannel()->wait();
                }
            }
        }

        return 0;
    }
}
