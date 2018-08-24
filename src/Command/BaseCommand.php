<?php
declare(strict_types=1);

namespace DepDoc\Command;

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
    protected $managerComposer;
    /** @var NodePackageManager */
    protected $managerNode;
    /** @var SymfonyStyle */
    protected $io;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->managerComposer = new ComposerPackageManager();
        $this->managerNode = new NodePackageManager();
    }

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

        return 0;
    }

    /**
     * @param string $directory
     * @return PackageManagerPackageList
     */
    protected function getInstalledPackages(string $directory): PackageManagerPackageList
    {
        $composer = $this->managerComposer;
        $node = $this->managerNode;

        $mergedPackageList = new PackageManagerPackageList();
        $mergedPackageList->merge($composer->getInstalledPackages($directory));
        $mergedPackageList->merge($node->getInstalledPackages($directory));

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
