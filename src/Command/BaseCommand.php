<?php
declare(strict_types=1);

namespace DepDoc\Command;

use DepDoc\Configuration\ApplicationConfiguration;
use DepDoc\Configuration\ConfigurationService;
use DepDoc\PackageManager\ComposerPackageManager;
use DepDoc\PackageManager\NodePackageManager;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use DepDoc\Parser\MarkdownParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BaseCommand extends Command
{
    /** @var ComposerPackageManager */
    protected $composerManager;
    /** @var NodePackageManager */
    protected $nodeManager;
    /** @var ConfigurationService */
    protected $configurationService;
    /** @var null|ApplicationConfiguration */
    protected $configuration;
    /** @var SymfonyStyle */
    protected $io;

    /**
     * @param string|null $name
     * @param ComposerPackageManager|null $managerComposer
     * @param NodePackageManager|null $managerNode
     * @param ConfigurationService|null $configurationService
     */
    public function __construct(
        string $name = null,
        ComposerPackageManager $managerComposer = null,
        NodePackageManager $managerNode = null,
        ConfigurationService $configurationService = null
    ) {
        $this->composerManager = $managerComposer ?? new ComposerPackageManager();
        $this->nodeManager = $managerNode ?? new NodePackageManager();
        $this->configurationService = $configurationService ?? new ConfigurationService();

        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption(
            '--directory',
            '-d',
            InputOption::VALUE_REQUIRED,
            'Target directory, default to current working directory',
            getcwd()
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $targetDirectory = $this->getTargetDirectoryFromInput($input);
        if (!$targetDirectory || realpath($targetDirectory) === false) {
            $this->io->error(sprintf(
                'Invalid target directory given: %s',
                $targetDirectory
            ));

            return -1;
        }

        if ($this->io->isVerbose()) {
            $this->io->writeln('<info>Target directory:</info> ' . $targetDirectory);
        }

        $this->configuration = $this->configurationService->loadFromDirectory($targetDirectory);
        if ($this->configuration === null) {
            $this->configuration = new ApplicationConfiguration();
        }

        return 0;
    }

    /**
     * @param string $directory
     * @return PackageManagerPackageList
     */
    protected function getInstalledPackages(string $directory): PackageManagerPackageList
    {
        $mergedPackageList = new PackageManagerPackageList();
        $mergedPackageList->merge($this->composerManager->getInstalledPackages($directory));
        $mergedPackageList->merge($this->nodeManager->getInstalledPackages($directory));

        return $mergedPackageList;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getTargetDirectoryFromInput(InputInterface $input): string
    {
        return (string)$input->getOption('directory');
    }

    /**
     * @param string $directory
     * @return string
     */
    protected function getAbsoluteFilepath(string $directory): string
    {
        return $directory . DIRECTORY_SEPARATOR . MarkdownParser::DEPENDENCIES_FILE;
    }
}
