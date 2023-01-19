<?php
declare(strict_types=1);

namespace DepDoc\Command;

use DepDoc\Configuration\ConfigurationService;
use DepDoc\PackageManager\ComposerPackageManager;
use DepDoc\PackageManager\NodePackageManager;
use DepDoc\Parser\ParserInterface;
use DepDoc\Validator\Formatter\DefaultFormatter;
use DepDoc\Validator\Formatter\FormatterInterface;
use DepDoc\Validator\Formatter\JUnitFormatter;
use DepDoc\Validator\PackageValidator;
use DepDoc\Validator\StrictMode;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
    public function __construct(
        PackageValidator $validator,
        ParserInterface $parser,
        ComposerPackageManager $managerComposer,
        NodePackageManager $managerNode,
        ConfigurationService $configurationService
    ) {
        parent::__construct('validate', $managerComposer, $managerNode, $configurationService);

        $this->validator = $validator;
        $this->parser = $parser;
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate an already generated DEPENDENCIES.md');

        $this->addOption(
            '--strict',
            null,
            InputOption::VALUE_OPTIONAL,
            'Strict mode checks for major and minor versions',
            false
        );

        $this->addOption(
            '--very-strict',
            null,
            InputOption::VALUE_OPTIONAL,
            'Very strict mode checks for full semantic versioning match',
            false
        );

        $this->addOption(
            'format',
            'f',
            InputOption::VALUE_REQUIRED,
            'Output format, could be "default" or "junit"',
            'default'
        );
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

        $validationResult = $this->validator->compare(
            $this->getStrictMode($input),
            $installedPackages,
            $dependencyList
        );

        $output = $this->getFormatter($input)->format($validationResult);

        if (count($validationResult) === 0) {
            if ($this->io->isVerbose()) {
                $this->io->writeln($output);
            }

            return 0;
        }

        $this->io->writeln($output);

        return -1;
    }

    protected function getStrictMode(InputInterface $input): StrictMode
    {
        if ($input->getOption('very-strict') !== false) {
            return StrictMode::fullSemVerMatch();
        }

        if ($input->getOption('strict') !== false) {
            return StrictMode::majorAndMinor();
        }

        return StrictMode::existingOrLocked();
    }

    protected function getFormatter(InputInterface $input): FormatterInterface
    {
        switch ($input->getOption('format')) {
            case 'default':
                return new DefaultFormatter();
            case 'junit':
                return new JUnitFormatter();
            default:
                throw new RuntimeException(sprintf(
                    'Invalid format "%s" given.',
                    $input->getOption('format')
                ));
        }
    }
}
