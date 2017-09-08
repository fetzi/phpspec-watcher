<?php

namespace Fetzi\PhpspecWatcher\Commands;

use Fetzi\PhpspecWatcher\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('initializes the phpspec watcher configuration file in the current working directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!Configuration::exists()) {
            Configuration::initialize();
            $io->success(
                sprintf('Successfully created the configuration file %s', Configuration::getConfigPath())
            );
        } else {
            $io->error('Configuration file already exists!');
        }
    }


}