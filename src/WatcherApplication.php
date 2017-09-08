<?php

namespace Fetzi\PhpspecWatcher;

use Fetzi\PhpspecWatcher\Commands\InitCommand;
use Fetzi\PhpspecWatcher\Commands\WatchCommand;
use Symfony\Component\Console\Application;

class WatcherApplication extends Application
{
    public function __construct()
    {
        parent::__construct('PHPSpec Watcher', '1.0');

        $this->add(new WatchCommand());
        $this->add(new InitCommand());
    }
}