<?php

namespace DepDoc\Writer;

use DepDoc\Dependencies\DependencyList;

class MarkdownWriter extends AbstractWriter
{
    public function createDocumentation(
        string $filepath,
        array $installedPackages,
        DependencyList $documentedDependencies
    ) {
        $documentation = [];

        foreach ($installedPackages as $packageManagerName => $packageManagerInstalledPackages) {

            if (count($packageManagerInstalledPackages) === 0) {
                continue;
            }

            $documentation[] = "### $packageManagerName";

            foreach ($packageManagerInstalledPackages as $installedPackage) {

                $documentation[] = "";

                $name = $installedPackage['name'];
                $version = $installedPackage['version'];
                $description = $installedPackage['description'];

                $documentedDependency = $documentedDependencies->get($packageManagerName, $name);

                // @TODO
                $lockedVersion = $documentedDependency['lockedVersion'] ?? null;
                $additionalContent = $documentedDependency['additionalContent'] ?? [];

                if ($lockedVersion) {
                    $usedLockSymbol = $documentedDependency['usedLockSymbol'] ?? '🔒';
                    $documentation[] = "##### $name `$lockedVersion` $usedLockSymbol";
                } else {
                    $documentation[] = "##### $name `$version`";
                }

                $documentation[] = "> $description";

                foreach ($additionalContent as $contentLine) {
                    $documentation[] = $contentLine;
                }
            }

            $documentation[] = "";
        }

        // @TODO: Maybe add documentation for packages who were documented but not installed (anymore)

        $documentation[] = "";

        $handle = @fopen($filepath, "w");

        foreach ($documentation as $line) {
            // @TODO: which line break?!
            fwrite($handle, "$line\r\n");
        }

        fclose($handle);
    }
}
