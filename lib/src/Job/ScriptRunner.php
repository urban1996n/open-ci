<?php

namespace App\Job;

use App\Pipeline\Component\Script;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class ScriptRunner
{
    private const TIMEOUT = 20000000;

    private ?LoggerInterface $logger = null;

    private OutputInterface $output;

    private bool $locked = false;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function run(Script $script, array $env = []): void
    {
        $command = $this->buildProcessFromScript($script);
        $command->setTimeout(self::TIMEOUT);

        try {
            $command->start($this->onScriptOutputCallback(), $env);
        } catch (ProcessFailedException $processFailedException) {

        } catch (ProcessTimedOutException $processTimedOutException) {

        } catch (ProcessSignaledException $processSignaledException) {

        }

        while ($command->isRunning()) {
            $this->locked = true;
            $script->setFinished(false);
        }

        $script->setFinished(true);
        $script->setSuccessful($command->isSuccessful());

        $this->locked = false;
    }

    private function onScriptOutputCallback(): \Closure
    {
        return function (string $type, string $line) : void {
            $this->output->writeln($this->composeMessage($type, $line));

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

    public function isLocked(): bool
    {
        return $this->locked;
    }
}