<?php
declare(strict_types=1);

namespace DepDoc\Parser;

use DepDoc\Configuration\ApplicationConfiguration;
use DepDoc\Dependencies\DependencyData;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use DepDoc\Parser\Exception\MissingFileException;
use DepDoc\Parser\Exception\ParseFailedException;

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

        if ($lines === false) {
            throw new ParseFailedException($filepath);
        }

        /** @var null|string $currentPackageManagerName */
        $currentPackageManagerName = null;
        /** @var null|string $currentPackage */
        $currentPackage = null;

        $dependencies = new PackageManagerPackageList();
        $currentDependency = null;

        foreach ($lines as $line) {

            $line = ltrim($line);

            if (preg_match("/^#{1}\s(?<packageManagerName>\w+)/", $line, $matches) === 1) {
                $currentPackageManagerName = $matches['packageManagerName'];
                $currentPackage = null;
                $currentDependency = null;
                continue;
            }

            if ($currentPackageManagerName === null) {
                continue;
            }

            if ($packageManagerName !== null && $packageManagerName !== $currentPackageManagerName) {
                continue;
            }

            $matches = null;
            $lockSymbolRegex = '(?<lockSymbol>' . implode('|', ApplicationConfiguration::ALLOWED_LOCK_SYMBOLS) . ')?';
            $packageAndVersionRegex = '/^#{2}\s(?<packageName>[^ ]+)\s`(?<version>[^`]+)`\s?' . $lockSymbolRegex . '/';

            if (preg_match($packageAndVersionRegex, $line, $matches) === 1) {
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

            if ($currentPackage === null || !$currentDependency instanceof DependencyData) {
                continue;
            }

            // Trim line breaks, because they will be added by the writer
            $trimmedLine = trim($line, "\n\r");

            $currentDependency->getAdditionalContent()->add($trimmedLine);
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
        foreach ($dependencies->getAllFlat() as $dependency) {
            // Search until first line with description (">") prefix was found; anything further is additional
            $descriptionFound = false;

            $additionalContent = $dependency->getAdditionalContent();
            foreach ($additionalContent->getAll() as $index => $contentLine) {
                if (strlen($contentLine) > 0 && $contentLine[0] === '>' && !$descriptionFound) {
                    $descriptionFound = true;
                    $additionalContent->removeIndex($index);

                    continue;
                }

                if ($contentLine === '') {
                    $priorLineIsEmpty = $additionalContent->getPreviousLine($index) === '';

                    if ($priorLineIsEmpty) {
                        $additionalContent->removeIndex($index);
                    }

                    continue;
                }
            }

            $additionalContent->removeLastEmptyLine();
        }
    }
}
