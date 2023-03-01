<?php

namespace App\Job;

use App\Job\Logger\Logger;
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

    public function __construct(private readonly Logger $logger)
    {
        $this->output = new ConsoleOutput();
    }

    public function run(Script $script, array $env): void
    {
        $command = $this->buildProcessFromScript($script);
        $command->setTimeout(self::TIMEOUT);

        try {
            $command->mustRun($this->onScriptOutputCallback($script), $env);
        } catch (ProcessFailedException|ProcessTimedOutException|ProcessSignaledException $processFailedException) {
            $this->logger->error($processFailedException->getMessage());
            $script->setSuccessful(false);
            $script->setFinished(true);
        }

        while ($command->isRunning() && $script->getSuccessful() === null) {
            $this->running = true;
            $script->setFinished(false);
        }

        $script->setFinished(true);

        $script->setSuccessful($script->getSuccessful() !== false && $command->isSuccessful());

        $this->running = false;
    }

    private function onScriptOutputCallback(Script $script): \Closure
    {
        dump('eee');
        return function (string $type, string $line) use ($script): void {
            $this->output->writeln($this->composeMessage($type, $line));

            if ($type === Process::ERR) {
                $script->setSuccessful(false);
            }

            $this->logger->log(
                $type === Process::ERR ? Level::Error : Level::Info,
                $line
            );
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
