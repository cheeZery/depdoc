<?php

namespace DepDoc\Command;

use DepDoc\PackageManager\ComposerPackageManager;
use DepDoc\PackageManager\NodePackageManager;
use DepDoc\Parser\ParserInterface;
use DepDoc\Parser\MarkdownParserInterface;
use DepDoc\Validator\PackageValidator;
use DepDoc\Writer\WriterInterface;
use DepDoc\Writer\MarkdownWriterInterface;
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
    /** @var ParserInterface */
    protected $parser;
    /** @var WriterInterface */
    protected $writer;
    /** @var PackageValidator */
    protected $validator;
    /** @var SymfonyStyle */
    protected $io;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->managerComposer = new ComposerPackageManager();
        $this->managerNode = new NodePackageManager();
        $this->parser = new MarkdownParserInterface();
        $this->writer = new MarkdownWriterInterface();
        $this->validator = new PackageValidator();
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
     * @return array
     */
    protected function getInstalledPackages(string $directory): array
    {
        $composer = $this->managerComposer;
        $node = $this->managerNode;

        return [
            $composer->getName() => $composer->getInstalledPackages($directory),
            $node->getName() => $node->getInstalledPackages($directory),
        ];
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
        return $directory . DIRECTORY_SEPARATOR . MarkdownParserInterface::DEPENDENCIES_FILE;
    }
}
