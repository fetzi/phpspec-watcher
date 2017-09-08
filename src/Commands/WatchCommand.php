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
        $io = new SymfonyStyle($input, $output);
        $this->displayInfo($io);

        (new Watcher($io, Configuration::load()))->start();
    }

    private function displayInfo(OutputStyle $io)
    {
        $io->title('PHPSpec Watcher');
        $io->text('PHPSpec tests will be automatically executed when a source or test file changes.');
        $io->newLine();
    }
}
