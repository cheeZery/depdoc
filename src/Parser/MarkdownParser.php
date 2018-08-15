<?php

namespace DepDoc\Parser;

use DepDoc\Dependencies\DependencyData;
use DepDoc\Dependencies\DependencyList;
use DepDoc\Parser\Exception\MissingFileException;

class MarkdownParser extends AbstractParser
{
    public const DEPENDENCIES_FILE = 'DEPENDENCIES.md';

    public function getDocumentedDependencies(string $filepath, ?string $packageManagerName = null): DependencyList
    {
        if (!file_exists($filepath)) {
            throw new MissingFileException($filepath);
        }

        $lines = file($filepath);
        $currentPackageManagerName = null;
        $currentPackage = null;

        $dependencies = new DependencyList();
        $currentDependency = null;

        foreach ($lines as $line) {

            $line = rtrim($line);

            if (preg_match("/^#{3}\s(\w+)/", $line, $matches)) {
                $currentPackageManagerName = $matches[1];
                $currentPackage = null;
                continue;
            }

            if (!$currentPackageManagerName) {
                continue;
            }

            if ($packageManagerName && $packageManagerName !== $currentPackageManagerName) {
                continue;
            }

            // @TODO: After config file was added, add option to define used lock symbol
            if (preg_match('/^#{5}\s([^ ]+)\s`([^`]+)`\s?(🔒|🛇|⚠|✋)?/', $line, $matches)) {
                $currentPackage = $matches[1];

                $currentDependency = new DependencyData(
                    $currentPackageManagerName,
                    $currentPackage,
                    isset($matches[3]) ? $matches[2] : null,
                    $matches[3] ?? null
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
     * @param DependencyList $dependencies
     */
    protected function cleanupAdditionalContent(DependencyList $dependencies): void
    {
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
