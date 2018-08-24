<?php
declare(strict_types=1);

namespace DepDoc\Writer;

use DepDoc\Dependencies\DependencyData;
use DepDoc\PackageManager\Package\ComposerPackage;
use DepDoc\PackageManager\Package\NodePackage;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;

class MarkdownWriter implements WriterInterface
{
    /**
     * @inheritdoc
     */
    public function createDocumentation(
        string $filepath,
        PackageManagerPackageList $installedPackages,
        PackageManagerPackageList $dependencyList,
        WriterConfiguration $configuration
    ) {
        $documentation = [];

        foreach ($installedPackages->getAll() as $packageManagerName => $packageManagerInstalledPackages) {

            if (count($packageManagerInstalledPackages) === 0) {
                continue;
            }

            $documentation[] = $this->createPackageManagerLine($packageManagerName);

            /** @var ComposerPackage|NodePackage $installedPackage */
            foreach ($packageManagerInstalledPackages as $installedPackage) {

                $documentation[] = "";

                $name = $installedPackage->getName();
                $version = $installedPackage->getVersion();
                $description = $installedPackage->getDescription();

                /** @var DependencyData $documentedDependency */
                $documentedDependency = $dependencyList->get($packageManagerName, $name);

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

        // @TODO: Maybe add documentation for packages which were documented but not installed (anymore)

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
        return "##### $packageName `$version` {$dependency->getLockSymbol()}";
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
     * @param null|string $description
     * @return string
     */
    protected function createDescriptionLine(?string $description): string
    {
        return "> $description";
    }
}
