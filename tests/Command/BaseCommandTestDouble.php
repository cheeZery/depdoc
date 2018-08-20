<?php
declare(strict_types=1);

namespace DepDocTest\Command;

use DepDoc\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommandTestDouble extends BaseCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function runExecute(InputInterface $input, OutputInterface $output): int
    {
        return $this->execute($input, $output);
    }
}
