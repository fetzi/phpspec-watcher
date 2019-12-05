<?php

namespace Fetzi\PhpspecWatcher;

use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Finder\Finder;

class WatcherFactory
{
    public static function create(OutputStyle $output, array $options): Watcher
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($options['directories'])
            ->name($options['fileMask']);

        $fileWatcher = new FileWatcher($finder);

        $phpspecCommand = sprintf('%s run', $options['phpspec']['binary']);

        foreach ($options['phpspec']['arguments'] as $argument) {
            $phpspecCommand .= sprintf(' --%s', $argument);
        }

        $loop = Factory::create();
        $stdio = new Stdio($loop);

        return new Watcher(
            $output,
            $fileWatcher,
            $loop,
            $stdio,
            $options['checkInterval'] ?? 1,
            $phpspecCommand,
            $options['notifications']['onSuccess'] ?? true,
            $options['notifications']['onError'] ?? true
        );
    }
}
