<?php

namespace Fetzi\PhpspecWatcher\Commands;

use Fetzi\PhpspecWatcher\Configuration;
use Fetzi\PhpspecWatcher\Watcher;
use Fetzi\PhpspecWatcher\WatcherFactory;
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

        WatcherFactory::create($output, Configuration::load())
            ->start();

        return 0;
    }

    private function displayInfo(OutputStyle $output)
    {
        $output->title('PHPSpec Watcher');
        $output->text('PHPSpec tests will be automatically executed when a source or test file changes.');
        $output->text('To manually trigger a test execution please press "t".');
        $output->newLine();
    }
}
