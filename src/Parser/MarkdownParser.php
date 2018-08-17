<?php
declare(strict_types=1);

namespace DepDoc\Parser;

use DepDoc\Dependencies\DependencyData;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use DepDoc\Parser\Exception\MissingFileException;

class MarkdownParser implements ParserInterface
{
    public const DEPENDENCIES_FILE = 'DEPENDENCIES.md';

    public function getDocumentedDependencies(
        string $filepath,
        ?string $packageManagerName = null
    ): PackageManagerPackageList {
        if (!file_exists($filepath)) {
            throw new MissingFileException($filepath);
        }

        $lines = file($filepath);
        /** @var null|string $currentPackageManagerName */
        $currentPackageManagerName = null;
        /** @var null|string $currentPackage */
        $currentPackage = null;

        $dependencies = new PackageManagerPackageList();
        $currentDependency = null;

        foreach ($lines as $line) {

            $line = rtrim($line);

            if (preg_match("/^#{3}\s(?<packageManagerName>\w+)/", $line, $matches)) {
                $currentPackageManagerName = $matches['packageManagerName'];
                $currentPackage = null;
                continue;
            }

            if ($currentPackageManagerName === null) {
                continue;
            }

            if ($packageManagerName && $packageManagerName !== $currentPackageManagerName) {
                continue;
            }

            // @TODO: After config file was added, add option to define used lock symbol
            if (preg_match('/^#{5}\s(?:<packageName>[^ ]+)\s`(?:<version>[^`]+)`\s?(?<lockSymbol>🔒|🛇|⚠|✋)?/', $line,
                $matches)) {
                $currentPackage = $matches['packageName'];

                $currentDependency = new DependencyData(
                    $currentPackageManagerName,
                    $currentPackage,
                    $matches['version'],
                    $matches['lockSymbol'] ?? null
                );
                $dependencies->add($currentDependency);

                continue;
            }

            if (!$currentPackage) {
                continue;
            }

            $currentDependency->getAdditionalContent()->add($line);
        }

        $this->cleanupAdditionalContent($dependencies);

        return $dependencies;
    }

    /**
     * @param PackageManagerPackageList $dependencies
     */
    protected function cleanupAdditionalContent(PackageManagerPackageList $dependencies): void
    {
        /** @var DependencyData $dependency */
        foreach ($dependencies as $dependency) {
            // Search until first line with description (">") prefix was found; anything further is additional
            $descriptionFound = false;
            // Used to save one empty line
            $priorLineWasEmpty = false;

            $additionalContent = $dependency->getAdditionalContent();
            foreach ($additionalContent->getAll() as $index => $contentLine) {
                if (strlen($contentLine) > 0 && $contentLine[0] === '>' && !$descriptionFound) {
                    $descriptionFound = true;
                    $additionalContent->removeIndex($index);

                    continue;
                }

                if ($contentLine === '') {
                    if ($priorLineWasEmpty) {
                        $additionalContent->removeIndex($index);
                    } else {
                        $priorLineWasEmpty = true;
                    }
                    continue;
                }

                $priorLineWasEmpty = false;
            }

            $additionalContent->removeLasEmptyLine();
        }
    }
}
