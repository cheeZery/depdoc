<?php

namespace DepDoc\Writer;

class Markdown extends Writer
{
    private const DEPENDENCIES_FILE = 'DEPENDENCIES.md';

    public function createDocumentation(array $installedPackages, array $documentedDependencies)
    {
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

                if (!empty($documentedDependencies[$packageManagerName])) {
                    $documentedDependency = $documentedDependencies[$packageManagerName][$name] ?? [];
                }

                $lockedVersion = $documentedDependency['lockedVersion'] ?? null;
                $additionalContent = $documentedDependency['additionalContent'] ?? [];

                if ($lockedVersion) {
                    $documentation[] = "##### $name `$lockedVersion` 🔒";
                } else {
                    $documentation[] = "##### $name `$version`";
                }

                $documentation[] = "> $description";

                foreach ($additionalContent as $contentLine) {
                    $documentation[] = $contentLine;
                }
            }

            // TODO: Maybe add documentation for packages who were documented but not installed (anymore)

            $documentation[] = "";

            $handle = @fopen(self::DEPENDENCIES_FILE, "w");

            foreach ($documentation as $line) {
                // TODO: which line break?!
                fwrite($handle, "$line\r\n");
            }

            fclose($handle);
        }
    }
}