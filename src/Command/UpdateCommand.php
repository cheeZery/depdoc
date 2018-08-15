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


        $filepath = $this->getAbsoluteFilepath($this->getTargetDirectoryFromInput($input));
        $directory = dirname($filepath);

        if (!file_exists($filepath)) {
            if ($this->io->isVerbose()) {
                $this->io->writeln(sprintf(
                    'Creating new file at: %s',
                    $filepath
                ));
            }

            touch($filepath);
        }

        $installedPackages = $this->getInstalledPackages($directory);

        $documentedDependencies = $this->parser->getDocumentedDependencies($filepath);
        if ($documentedDependencies === null) {
            return -1;
        }

        $this->writer->createDocumentation($filepath, $installedPackages, $documentedDependencies);

        return 0;
    }
}
