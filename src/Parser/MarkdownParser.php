<?php

namespace DepDoc\Parser;

class MarkdownParser extends AbstractParser
{
    private const DEPENDENCIES_FILE = 'DEPENDENCIES.md';

    public function getDocumentedDependencies(?string $packageManagerName = null): ?array
    {
        if (!file_exists(self::DEPENDENCIES_FILE)) {
            echo self::DEPENDENCIES_FILE . ' is missing!';

            return null;
        }

        $handle = @fopen(self::DEPENDENCIES_FILE, "r");

        $currentPackageManagerName = null;
        $currentPackage = null;

        $dependencies = [];

        while (($line = fgets($handle)) !== false) {

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

            if (empty($dependencies[$currentPackageManagerName])) {
                $dependencies[$currentPackageManagerName] = [];
            }

            // TODO: After config file was added, add option to define used lock symbol
            if (preg_match('/^#{5}\s([^ ]+)\s`([^`]+)`\s?(🔒|🛇|⚠|✋)?/', $line, $matches)) {
                $currentPackage = $matches[1];

                // TODO: Create model for documented dependency
                $dependencies[$currentPackageManagerName][$currentPackage] = [
                    'name' => $currentPackage,
                    'lockedVersion' => isset($matches[3]) ? $matches[2] : null,
                    'usedLockSymbol' => $matches[3] ?? null,
                    'additionalContent' => [],
                ];
                continue;
            }

            if (!$currentPackage) {
                continue;
            }

            $dependencies[$currentPackageManagerName][$currentPackage]['additionalContent'][] = $line;
        }

        fclose($handle);

        foreach ($dependencies as &$packageManagerDependencies) {
            foreach ($packageManagerDependencies as &$dependency) {
                $descriptionFound = false;
                $priorLineWasEmpty = false;

                foreach ($dependency['additionalContent'] as $index => $contentLine) {
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
        }

        if ($packageManagerName) {
            return $dependencies[$packageManagerName] ?? [];
        }

        return $dependencies;
    }
}
