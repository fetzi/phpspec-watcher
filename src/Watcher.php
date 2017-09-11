<?php

namespace Fetzi\PhpspecWatcher;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;
use Yosymfony\ResourceWatcher\ResourceWatcher;

class Watcher
{
    /**
     * @var OutputStyle
     */
    private $output;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var LoopInterface
     */
    private $loop;

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

    public function __construct(
        OutputStyle $output,
        Finder $finder,
        LoopInterface $loop,
        int $checkInterval,
        string $phpspecCommand,
        bool $notifyOnSuccess,
        bool $notifyOnError
    )
    {
        $this->output = $output;
        $this->finder = $finder;
        $this->loop = $loop;
        $this->checkInterval = $checkInterval;
        $this->phpspecCommand = $phpspecCommand;
        $this->notifyOnSuccess = $notifyOnSuccess;
        $this->notifyOnError = $notifyOnError;
    }

    public function start()
    {
        $resourceCache = new ResourceCacheMemory();
        $resourceWatcher = new ResourceWatcher($resourceCache);
        $resourceWatcher->setFinder($this->finder);

        $this->loop->addPeriodicTimer($this->checkInterval, function () use ($resourceWatcher) {
            $resourceWatcher->findChanges();

            if ($resourceWatcher->hasChanges()) {
                $this->output->writeln('starting tests');
                if ($this->runTests()) {
                    $this->notifySuccess();
                } else {
                    $this->notifyError();
                }

                $this->output->newLine(2);
                $this->output->writeln('waiting for changes ...');
            }
        });

        $this->loop->run();
    }

    private function runTests() : bool
    {
        $process = new Process($this->phpspecCommand);
        $process->setTty(true);
        $process->run();

        return $process->isSuccessful();
    }

    private function notifySuccess()
    {
        if ($this->notifyOnSuccess) {
            Notification::create(
                'Tests passed',
                Notification::ICON_SUCCESS
            )->send();
        }
    }

    private function notifyError()
    {
        if ($this->notifyOnError) {
            Notification::create(
                'Tests failed',
                Notification::ICON_ERROR
            )->send();
        }
    }
}
