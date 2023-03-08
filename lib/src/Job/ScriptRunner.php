<?php

namespace App\Job;

use App\Common\Status;
use App\Pipeline\Data\Component\Script;
use Monolog\Level;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class ScriptRunner
{
    private const TIMEOUT = 20000000;

    private OutputInterface $output;

    private bool $running = false;

    private ?\Closure $logger = null;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    /** @param \Closure<int, string> */
    public function run(Script $script, array $env, \Closure $logger): void
    {
        $this->logger = $logger;
        $command      = $this->buildProcessFromScript($script);
        $command->setTimeout(self::TIMEOUT);

        try {
            $command->mustRun($this->onScriptOutputCallback($script), $env);
        } catch (ProcessFailedException|ProcessTimedOutException|ProcessSignaledException $processFailedException) {
            $this->logger->call($this, 'error', $processFailedException->getMessage());
            $script->setStatus(Status::Failure);
            $script->setFinished(true);
        }

        while ($command->isRunning() && $script->getStatus() === null) {
            $this->running = true;
            $script->setStatus(Status::Pending);
            $script->setFinished(false);
        }

        $script->setFinished(true);

        if (!$script->getStatus()) {
            $script->setStatus($command->isSuccessful() ? Status::Success : Status::Failure);
        }

        $this->running = false;
    }

    private function onScriptOutputCallback(Script $script): \Closure
    {
        return function (string $type, string $line) use ($script): void {
            $this->output->writeln($this->composeMessage($type, $line));

            if ($type === Process::ERR) {
                $script->setStatus(Status::Failure);
            }

            $type = $type === Process::ERR ? Level::Error : Level::Info;

            $this->logger->call($this, $type->toPsrLogLevel(), $line);
        };
    }

    private function composeMessage(string $type, string $message): string
    {
        $template = $type === Process::ERR ? '<error>%s</error>' : '<info>%s</info>';

        return \sprintf($template, $message);
    }

    private function buildProcessFromScript(Script $script): Process
    {
        $command = $script->getContext()
            ? $script->getContext() . '/' . $script->getCommand()
            : $script->getCommand();

        return new Process([$script->getBinary(), $command, ...$script->getArgs()]);
    }

    public function isRunning(): bool
    {
        return $this->running;
    }
}
