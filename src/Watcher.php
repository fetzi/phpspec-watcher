<?php

namespace Fetzi\PhpspecWatcher;

use Clue\React\Stdio\Stdio;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Process\Process;

class Watcher
{
    /**
     * @var OutputStyle
     */
    private $output;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var Stdio
     */
    private $stdio;

    /**
     * @var int
     */
    private $checkInterval;

    /**
     * @var string
     */
    private $phpspecCommand;

    /**
     * @var bool
     */
    private $notifyOnSuccess;

    /**
     * @var bool
     */
    private $notifyOnError;

    /**
     * @var FileWatcher
     */
    private $fileWatcher;

    public function __construct(
        OutputStyle $output,
        FileWatcher $fileWatcher,
        LoopInterface $loop,
        Stdio $stdio,
        int $checkInterval,
        string $phpspecCommand,
        bool $notifyOnSuccess,
        bool $notifyOnError
    ) {
        $this->output = $output;
        $this->fileWatcher = $fileWatcher;
        $this->loop = $loop;
        $this->stdio = $stdio;
        $this->checkInterval = $checkInterval;
        $this->phpspecCommand = $phpspecCommand;
        $this->notifyOnSuccess = $notifyOnSuccess;
        $this->notifyOnError = $notifyOnError;
    }

    public function start()
    {
        $executeFunc = function () {
            if ($this->fileWatcher->hasChanges()) {
                $this->runTests();
            }
        };

        $this->stdio->on('t', function () {
            $this->runTests();
        });
        $this->loop->addPeriodicTimer($this->checkInterval, $executeFunc);

        $this->loop->run();
    }

    private function runTests()
    {
        $this->output->writeln(sprintf('Starting tests (<fg=cyan>%s</>)', $this->phpspecCommand));
        $this->output->newLine();

        $process = new Process($this->phpspecCommand);
        $process->setTty(true);
        $process->run();

        if ($process->isSuccessful()) {
            $this->notifySuccess();
        } else {
            $this->notifyError();
        }

        $this->output->newLine();
        $this->output->write('Waiting for changes, <fg=yellow>to manually trigger a test execution
        please press <fg=yellow;options=bold>"t"</></>');
        $this->output->newLine();
    }

    private function notifySuccess()
    {
        $this->output->newLine();
        $this->output->writeln('<fg=green;options=bold>All tests passed!</>');

        if ($this->notifyOnSuccess) {
            Notification::create(
                'Tests passed',
                Notification::ICON_SUCCESS
            )->send();
        }
    }

    private function notifyError()
    {
        $this->output->newLine();
        $this->output->writeln('<fg=red;options=bold>Test exection failed!</>');

        if ($this->notifyOnError) {
            Notification::create(
                'Tests failed',
                Notification::ICON_ERROR
            )->send();
        }
    }
}
