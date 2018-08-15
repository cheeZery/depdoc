<?php
declare(strict_types=1);

namespace DepDoc\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('validate')
            ->setDescription('Validate a already generated DEPENDENCIES.md');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = parent::execute($input, $output);
        if ($exitCode !== 0) {
            return $exitCode;
        }


        $filepath = $this->getAbsoluteFilepath($this->getTargetDirectoryFromInput($input));
        $directory = dirname($filepath);

        if (!file_exists($filepath)) {
            $this->io->error(sprintf(
                'Missing dependency file in: %s',
                $filepath
            ));

            return -1;
        }

        $installedPackages = $this->getInstalledPackages($directory);

        $documentedDependencies = $this->parser->getDocumentedDependencies($filepath);
        if ($documentedDependencies === null) {
            return -1;
        }

        $validationResult = $this->validator->compare($installedPackages, $documentedDependencies);
        if (empty($validationResult)) {
            return 0;
        }

        $this->io->error(sprintf(
            'Validation result: found %s error(s)',
            count($validationResult)
        ));

        foreach ($validationResult as $line) {
            $output->writeln($line->toString());
        }

        return -1;
    }
}
