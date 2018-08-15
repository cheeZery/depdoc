<?php

namespace DepDoc\Parser;

use DepDoc\Dependencies\DependencyData;
use DepDoc\Dependencies\DependencyList;
use DepDoc\Parser\Exception\MissingFileException;

class MarkdownParser extends AbstractParser
{
    public const DEPENDENCIES_FILE = 'DEPENDENCIES.md';

    public function getDocumentedDependencies(string $filepath, ?string $packageManagerName = null): ?array
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

            $currentDependency->addAdditionalContent($line);
        }

        foreach ($dependencies as $dependency) {
            $descriptionFound = false;
            $priorLineWasEmpty = false;

            foreach ($dependency->getAdditionalContent() as $index => $contentLine) {
                if (strlen($contentLine) > 0 && $contentLine[0] === '>' && !$descriptionFound) {
                    $descriptionFound = true;
                    unset($dependency['additionalContent'][$index]);
                    continue;
                }

                if ($contentLine === '') {
                    if ($priorLineWasEmpty) {
                        unset($dependency['additionalContent'][$index]);
                    } else {
                        $priorLineWasEmpty = true;
                    }
                    continue;
                }

                $priorLineWasEmpty = false;
            }

            if (end($dependency['additionalContent']) === "") {
                array_pop($dependency['additionalContent']);
            }
        }

        if ($packageManagerName) {
            return $dependencies[$packageManagerName] ?? [];
        }

        return $dependencies;
    }
}
