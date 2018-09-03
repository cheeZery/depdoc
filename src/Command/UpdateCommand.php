<?php
declare(strict_types=1);

namespace DepDoc\Command;

use DepDoc\Parser\MarkdownParser;
use DepDoc\Parser\ParserInterface;
use DepDoc\Writer\MarkdownWriter;
use DepDoc\Writer\WriterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends BaseCommand
{
    /** @var WriterInterface */
    protected $writer;
    /** @var ParserInterface */
    protected $parser;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->writer = new MarkdownWriter();
        $this->parser = new MarkdownParser();

        parent::__construct('update');
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->setDescription('Update or create a DEPENDENCIES.md');
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

        $this->writer->getConfiguration()->setNewline($this->configuration->getNewlineCharacter());
        $this->writer->createDocumentation($filepath, $installedPackages, $documentedDependencies);

        return 0;
    }
}
