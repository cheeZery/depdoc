<?php
declare(strict_types=1);

namespace DepDoc;

use DepDoc\Helper\CliCommandHelper;

class Application
{
    private CliCommandHelper $cliCommandHelper;

    public function __construct()
    {
        $this->cliCommandHelper = new CliCommandHelper();
    }

    public function updateAction()
    {
        $composer = new PackageManager\Composer($this->cliCommandHelper);
        $node = new PackageManager\Node($this->cliCommandHelper);

        $installedPackages[$composer->getName()] = $composer->getInstalledPackages();
        $installedPackages[$node->getName()] = $node->getInstalledPackages();

        $markdownParser = new Parser\Markdown();
        $documentedDependencies = $markdownParser->getDocumentedDependencies();

        $writer = new Writer\Markdown();
        $writer->createDocumentation($installedPackages, $documentedDependencies);
    }

    public function validateAction()
    {
        $composer = new PackageManager\Composer($this->cliCommandHelper);
        $node = new PackageManager\Node($this->cliCommandHelper);

        $installedPackages[$composer->getName()] = $composer->getInstalledPackages();
        $installedPackages[$node->getName()] = $node->getInstalledPackages();

        $markdownParser = new Parser\Markdown();
        $documentedDependencies = $markdownParser->getDocumentedDependencies();

        $validator = new Validator\Validator();
        $validator->compare($installedPackages, $documentedDependencies);
    }
}
