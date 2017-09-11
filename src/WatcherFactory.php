<?php

namespace Fetzi\PhpspecWatcher;

use React\EventLoop\Factory;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Finder\Finder;

class WatcherFactory
{
    public static function create(OutputStyle $output, array $options) : Watcher
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($options['directories'])
            ->name($options['fileMask']);

        $phpspecCommand = sprintf('%s run', $options['phpspec']['binary']);

        foreach($options['phpspec']['arguments'] as $argument) {
            $phpspecCommand .= sprintf(' --%s', $argument);
        }

        return new Watcher(
            $output,
            $finder,
            Factory::create(),
            $options['checkInterval'] ?? 1,
            $phpspecCommand,
            $options['notifications']['onSuccess'] ?? true,
            $options['notifications']['onError'] ?? true
        );
    }
}
