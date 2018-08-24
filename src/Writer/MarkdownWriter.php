<?php
declare(strict_types=1);

namespace DepDoc\Writer;

use DepDoc\Dependencies\DependencyData;
use DepDoc\PackageManager\Package\ComposerPackage;
use DepDoc\PackageManager\Package\NodePackage;
use DepDoc\PackageManager\Package\PackageManagerPackageInterface;
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

                /** @var DependencyData $documentedDependency */
                $documentedDependency = $dependencyList->get($packageManagerName, $installedPackage->getName());

                if ($documentedDependency && $documentedDependency->isVersionLocked()) {
                    $documentation[] = $this->createPackageLockedLine(
                        $installedPackage,
                        $documentedDependency,
                        $configuration->isExportExternalLink()
                    );
                } else {
                    $documentation[] = $this->createPackageLine(
                        $installedPackage,
                        $configuration->isExportExternalLink()
                    );
                }

                $documentation[] = $this->createDescriptionLine($installedPackage->getDescription());

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
     * @param PackageManagerPackageInterface $package
     * @param DependencyData $dependency
     * @param bool $exportExternalLink
     * @return string
     */
    protected function createPackageLockedLine(
        PackageManagerPackageInterface $package,
        DependencyData $dependency,
        bool $exportExternalLink
    ): string {
        $line = "##### {$package->getName()} `{$package->getVersion()}` {$dependency->getLockSymbol()}";
        if ($exportExternalLink) {
            $line .= " {$this->createExternalLink($package)}";
        }

        return $line;
    }

    /**
     * @param PackageManagerPackageInterface $package
     * @param bool $exportExternalLink
     * @return string
     */
    protected function createPackageLine(PackageManagerPackageInterface $package, bool $exportExternalLink): string
    {
        $line = "##### {$package->getName()} `{$package->getVersion()}`";
        if ($exportExternalLink) {
            $line .= " {$this->createExternalLink($package)}";
        }

        return $line;
    }

    /**
     * @param null|string $description
     * @return string
     */
    protected function createDescriptionLine(?string $description): string
    {
        return "> $description";
    }

    /**
     * @param PackageManagerPackageInterface $package
     * @return string
     */
    protected function createExternalLink(PackageManagerPackageInterface $package): string
    {
        return " [link]({$package->getExternalLink()})";
    }
}
