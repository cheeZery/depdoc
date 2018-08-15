<?php

namespace DepDoc\Writer;

class MarkdownWriter extends AbstractWriter
{
    public function createDocumentation(
        string $filePath,
        array $installedPackages,
        array $documentedDependencies
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

                if (!empty($documentedDependencies[$packageManagerName])) {
                    $documentedDependency = $documentedDependencies[$packageManagerName][$name] ?? [];
                }

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

            // @TODO: Maybe add documentation for packages who were documented but not installed (anymore)

            $documentation[] = "";
        }

        $this->writeDocumentation($filePath, $documentation);
    }

    protected function writeDocumentation(
        string $filePath,
        iterable $documentation
    ): void {
        $handle = @fopen($filePath, "w");

        foreach ($documentation as $line) {
            fwrite($handle, $line . PHP_EOL);
        }

        fclose($handle);
    }
}
