<?php
declare(strict_types=1);

namespace DepDoc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    protected function configure()
    {
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
