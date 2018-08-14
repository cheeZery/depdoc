<?php

namespace DepDoc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this->addOption(
            '--directory',
            '-d',
            InputOption::VALUE_REQUIRED,
            'Target directory, default to current working directory'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $targetDirectory = $input->getOption('directory') ?? getcwd();
        if (!$targetDirectory || realpath($targetDirectory) === false) {
            $output->writeln('<error>Invalid target directory given.</error>');

            return -1;
        }

        if ($output->isVerbose()) {
            $output->writeln('<info>Target directory:</info> ' . $targetDirectory);
        }

        return 0;
    }
}
