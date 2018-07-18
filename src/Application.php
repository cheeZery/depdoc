<?php
declare(strict_types=1);

namespace DepDoc;

class Application
{
    public function updateAction(): bool
    {
        $composer = new PackageManager\Composer();
        $installedPackages[$composer->getName()] = $composer->getInstalledPackages();
        $node = new PackageManager\Node();
        $installedPackages[$node->getName()] = [];
        $installedPackages[$node->getName()] = $node->getInstalledPackages();

        $markdownParser = new Parser\Markdown();
        $documentedDependencies = $markdownParser->getDocumentedDependencies();

        $writer = new Writer\Markdown();
        $writer->createDocumentation($installedPackages, $documentedDependencies);

        return true;
    }

    public function validateAction(): bool
    {
        $composer = new PackageManager\Composer();
        $installedPackages[$composer->getName()] = $composer->getInstalledPackages();
        $node = new PackageManager\Node();
        $installedPackages[$node->getName()] = $node->getInstalledPackages();

        $markdownParser = new Parser\Markdown();
        $documentedDependencies = $markdownParser->getDocumentedDependencies();

        $validator = new Validator\Validator();
        $validator->compare($installedPackages, $documentedDependencies);

        return true;
    }
}
