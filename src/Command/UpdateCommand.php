<?php
declare(strict_types=1);

namespace DepDoc\Command;

use DepDoc\Parser\MarkdownParser;
use DepDoc\Parser\ParserInterface;
use DepDoc\Writer\MarkdownWriter;
use DepDoc\Writer\WriterConfiguration;
use DepDoc\Writer\WriterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends BaseCommand
{
    /** @var WriterInterface */
    protected $writer;
    /** @var ParserInterface */
    protected $parser;

    public function __construct()
    {
        parent::__construct('update');

        $this->writer = new MarkdownWriter();
        $this->parser = new MarkdownParser();
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Update or create a DEPENDENCIES.md')
            ->addOption(
                'newline',
                'l',
                InputOption::VALUE_REQUIRED,
                'Newline character(s) used to separate the file content',
                PHP_EOL
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = parent::execute($input, $output);
        if ($exitCode !== 0) {
            return $exitCode;
        }

        $newline = $input->getOption('newline');

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

        $this->writer->createDocumentation($filepath, $installedPackages, $documentedDependencies,
            new WriterConfiguration(
                $newline
            ));

        return 0;
    }
}
