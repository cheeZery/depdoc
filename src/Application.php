<?php
declare(strict_types=1);

namespace DepDoc;

use DepDoc\PackageManager\ComposerPackageManager;
use DepDoc\PackageManager\NodePackageManager;
use DepDoc\Parser\AbstractParser;
use DepDoc\Parser\MarkdownParser;
use DepDoc\Validator\PackageValidator;
use DepDoc\Writer\AbstractWriter;
use DepDoc\Writer\MarkdownWriter;

class Application
{
    /** @var ComposerPackageManager */
    protected $managerComposer;
    /** @var NodePackageManager */
    protected $managerNode;
    /** @var AbstractParser */
    protected $parser;
    /** @var AbstractWriter */
    protected $writer;
    /** @var PackageValidator */
    protected $validator;

    public function __construct()
    {
        $this->managerComposer = new ComposerPackageManager();
        $this->managerNode = new NodePackageManager();
        $this->parser = new MarkdownParser();
        $this->writer = new MarkdownWriter();
    }

    public function updateAction(): bool
    {
        $composer = $this->getManagerComposer();
        $node = $this->getManagerNode();

        $installedPackages[$composer->getName()] = $composer->getInstalledPackages();
        $installedPackages[$node->getName()] = [];
        $installedPackages[$node->getName()] = $node->getInstalledPackages();

        $documentedDependencies = $this->getParser()
            ->getDocumentedDependencies();
        if ($documentedDependencies === null) {
            return false;
        }

        $this->getWriter()
            ->createDocumentation($installedPackages, $documentedDependencies);

        return true;
    }

    public function validateAction(): bool
    {
        $composer = $this->getManagerComposer();
        $node = $this->getManagerNode();

        $installedPackages[$composer->getName()] = $composer->getInstalledPackages();
        $installedPackages[$node->getName()] = $node->getInstalledPackages();

        $documentedDependencies = $this->getParser()
            ->getDocumentedDependencies();
        if ($documentedDependencies === null) {
            return false;
        }

        $validationResult = $this->getValidator()
            ->compare($installedPackages, $documentedDependencies);

        if (empty($validationResult)) {
            return true;
        }

        foreach ($validationResult as $line) {
            echo $line  . PHP_EOL;
        }

        return false;
    }

    /**
     * @return ComposerPackageManager
     */
    public function getManagerComposer(): ComposerPackageManager
    {
        return $this->managerComposer;
    }

    /**
     * @param ComposerPackageManager $managerComposer
     * @return $this
     */
    public function setManagerComposer(ComposerPackageManager $managerComposer): Application
    {
        $this->managerComposer = $managerComposer;

        return $this;
    }

    /**
     * @return NodePackageManager
     */
    public function getManagerNode(): NodePackageManager
    {
        return $this->managerNode;
    }

    /**
     * @param NodePackageManager $managerNode
     * @return $this
     */
    public function setManagerNode(NodePackageManager $managerNode): Application
    {
        $this->managerNode = $managerNode;

        return $this;
    }

    /**
     * @return AbstractParser
     */
    public function getParser(): AbstractParser
    {
        return $this->parser;
    }

    /**
     * @param AbstractParser $parser
     * @return $this
     */
    public function setParser(AbstractParser $parser): Application
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @return AbstractWriter
     */
    public function getWriter(): AbstractWriter
    {
        return $this->writer;
    }

    /**
     * @param AbstractWriter $writer
     * @return $this
     */
    public function setWriter(AbstractWriter $writer): Application
    {
        $this->writer = $writer;

        return $this;
    }

    /**
     * @return PackageValidator
     */
    public function getValidator(): PackageValidator
    {
        return $this->validator;
    }

    /**
     * @param PackageValidator $validator
     * @return $this
     */
    public function setValidator(PackageValidator $validator): Application
    {
        $this->validator = $validator;

        return $this;
    }
}
