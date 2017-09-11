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
     * @var array
     */
    private $options;

    public function __construct(OutputStyle $output, array $options)
    {
        $this->output = $output;
        $this->options = $options;

        $this->finder = new Finder();
        $this->finder
            ->files()
            ->in($options['directories'])
            ->name($options['fileMask']);

        $this->loop = Factory::create();
    }

    public function start()
    {
        $resourceCache = new ResourceCacheMemory();
        $resourceWatcher = new ResourceWatcher($resourceCache);
        $resourceWatcher->setFinder($this->finder);

        $this->loop->addPeriodicTimer($this->options['checkInterval'], function () use ($resourceWatcher) {
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
        $process = new Process(
            sprintf('%s run', $this->options['phpspecBinary'])
        );
        $process->setTty(true);

        return $process->isSuccessful();
    }

    private function notifySuccess()
    {
        if ($this->options['notifications']['onSuccess']) {
            Notification::create(
                'Tests passed',
                __DIR__.'/../assets/success.png'
            )->send();
        }
    }

    private function notifyError()
    {
        if ($this->options['notifications']['onError']) {
            Notification::create(
                'Tests failed',
                __DIR__.'/../assets/error.png'
            )->send();
        }
    }
}
