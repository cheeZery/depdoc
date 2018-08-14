<?php
declare(strict_types=1);

namespace DepDoc\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('update')
            ->setDescription('Update or create a DEPENDENCIES.md');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = parent::execute($input, $output);
        if ($exitCode !== 0) {
            return $exitCode;
        }

        return 0;
    }
}
