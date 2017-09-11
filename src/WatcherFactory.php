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

        $phpspecCommand = sprintf('%s run', $options['phpspecBinary']);

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
