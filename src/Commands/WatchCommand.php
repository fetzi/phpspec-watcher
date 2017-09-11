<?php

namespace Fetzi\PhpspecWatcher\Commands;

use Fetzi\PhpspecWatcher\Configuration;
use Fetzi\PhpspecWatcher\Watcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

class WatchCommand extends Command
{
    protected function configure()
    {
        $this->setName('watch')
            ->setDescription('Watches for file changes and triggers phpspec tests');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);
        $this->displayInfo($output);

        (new Watcher($output, Configuration::load()))->start();
    }

    private function displayInfo(OutputStyle $output)
    {
        $output->title('PHPSpec Watcher');
        $output->text('PHPSpec tests will be automatically executed when a source or test file changes.');
        $output->newLine();
    }
}
