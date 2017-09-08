<?php

namespace Fetzi\PhpspecWatcher;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
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
    private $io;

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

    public function __construct(OutputStyle $io, array $options)
    {
        $this->io = $io;
        $this->options = $options;

        $this->finder =  new Finder();
        $this->finder
            ->files()
            ->in($options['directories'])
            ->name($options['fileMask']);

        $this->loop = Factory::create();
        $this->notifier = NotifierFactory::create();
    }

    public function start()
    {
        $resourceCache = new ResourceCacheMemory();
        $resourceWatcher = new ResourceWatcher($resourceCache);
        $resourceWatcher->setFinder($this->finder);

        $this->loop->addPeriodicTimer($this->options['checkInterval'], function() use ($resourceWatcher) {
            $resourceWatcher->findChanges();

            if ($resourceWatcher->hasChanges()) {
                $this->io->writeln('starting tests');
                if ($this->runTests()) {
                    $this->notifySuccess();
                } else {
                    $this->notifyError();
                }

                $this->io->newLine(2);
                $this->io->writeln('waiting for changes ...');
            }
        });

        $this->loop->run();
    }

    private function runTests()
    {
        $process = new Process(
            sprintf('%s run', $this->options['phpspecBinary'])
        );
        $process->setTty(true);

        return $process->run() === 0;
    }

    private function notifySuccess()
    {
        if ($this->options['notifications']['onSuccess']) {
            $notification = (new Notification())
                ->setTitle('PHPSpec Watcher')
                ->setBody('Tests passed')
                ->setIcon(__DIR__ . '/../assets/success.png');

            $this->notifier->send($notification);
        }
    }

    private function notifyError()
    {
        if ($this->options['notifications']['onError']) {
            $notification = (new Notification())
                ->setTitle('PHPSpec Watcher')
                ->setBody('Tests failed')
                ->setIcon(__DIR__ . '/../assets/error.png');

            $this->notifier->send($notification);
        }
    }
}