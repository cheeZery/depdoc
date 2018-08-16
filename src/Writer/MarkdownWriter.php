<?php

namespace DepDoc\Writer;

use DepDoc\Dependencies\DependencyData;
use DepDoc\Dependencies\DependencyList;

class MarkdownWriter extends AbstractWriter
{
    public function createDocumentation(
        string $filepath,
        array $installedPackages,
        DependencyList $documentedDependencies,
        WriterConfiguration $configuration
    ) {
        $documentation = [];

        foreach ($installedPackages as $packageManagerName => $packageManagerInstalledPackages) {

            if (count($packageManagerInstalledPackages) === 0) {
                continue;
            }

            $documentation[] = $this->createPackageManagerLine($packageManagerName);

            foreach ($packageManagerInstalledPackages as $installedPackage) {

                $documentation[] = "";

                $name = $installedPackage['name'];
                $version = $installedPackage['version'];
                $description = $installedPackage['description'];

                $documentedDependency = $documentedDependencies->get($packageManagerName, $name);

                if ($documentedDependency && $documentedDependency->isVersionLocked()) {
                    $documentation[] = $this->createPackageLockedLine($name, $version, $documentedDependency);
                } else {
                    $documentation[] = $this->createPackageLine($name, $version);
                }

                $documentation[] = $this->createDescriptionLine($description);

                if ($documentedDependency) {
                    foreach ($documentedDependency->getAdditionalContent()->getAll() as $contentLine) {
                        $documentation[] = $contentLine;
                    }
                }
            }

            $documentation[] = "";
        }

        // @TODO: Maybe add documentation for packages who were documented but not installed (anymore)

        $documentation[] = "";

        $handle = @fopen($filepath, "w");

        foreach ($documentation as $line) {
            fwrite($handle, $line . $configuration->getNewline());
        }

        fclose($handle);
    }

    /**
     * @param string $packageManagerName
     * @return string
     */
    protected function createPackageManagerLine(string $packageManagerName): string
    {
        return "### $packageManagerName";
    }

    /**
     * @param string $packageName
     * @param string $version
     * @param DependencyData $dependency
     * @return string
     */
    protected function createPackageLockedLine(string $packageName, string $version, DependencyData $dependency): string
    {
        return "##### $packageName `$version` {$dependency->getVersionLockSymbol()}";
    }

    /**
     * @param string $packageName
     * @param string $version
     * @return string
     */
    protected function createPackageLine(string $packageName, string $version): string
    {
        return "##### $packageName `$version`";
    }

    /**
     * @param string $description
     * @return string
     */
    protected function createDescriptionLine(string $description): string
    {
        return "> $description";
    }
}
