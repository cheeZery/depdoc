<?php
declare(strict_types=1);

namespace DepDoc\Command;

use DepDoc\Parser\ParserInterface;
use DepDoc\Validator\PackageValidator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateCommand extends BaseCommand
{
    /** @var ParserInterface */
    protected $parser;
    /** @var PackageValidator */
    protected $validator;

    /**
     * @inheritdoc
     */
    public function __construct(PackageValidator $validator, ParserInterface $parser)
    {
        parent::__construct('validate');

        $this->validator = $validator;
        $this->parser = $parser;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->setDescription('Validate a already generated DEPENDENCIES.md');
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
        $dependencyList = $this->parser->getDocumentedDependencies($filepath);

        $validationResult = $this->validator->compare($installedPackages, $dependencyList);
        if (empty($validationResult)) {
            if ($this->io->isVerbose()) {
                $this->io->writeln('Validation result: empty, all fine.');
            }

            return 0;
        }

        $this->io->error(sprintf(
            'Validation result: found %s error(s)',
            count($validationResult)
        ));

        foreach ($validationResult as $line) {
            $this->io->writeln($line->toString());
        }

        return -1;
    }
}
