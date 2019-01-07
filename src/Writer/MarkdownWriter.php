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
    /** @var WriterConfiguration */
    protected $configuration;

    /**
     * @param WriterConfiguration|null $configuration
     */
    public function __construct(?WriterConfiguration $configuration = null)
    {
        $this->configuration = $configuration ?? new WriterConfiguration();
    }


    /**
     * @inheritdoc
     */
    public function createDocumentation(
        string $filepath,
        PackageManagerPackageList $installedPackages,
        PackageManagerPackageList $dependencyList
    ): void {
        $documentation = [];

        foreach ($installedPackages->getAll() as $packageManagerName => $packageManagerInstalledPackages) {

            if (count($packageManagerInstalledPackages) === 0) {
                continue;
            }

            $documentation[] = $this->createPackageManagerLine($packageManagerName);

            /** @var ComposerPackage|NodePackage $installedPackage */
            foreach ($packageManagerInstalledPackages as $installedPackage) {

                $documentation[] = "";

                /** @var DependencyData $documentedDependency|null */
                $documentedDependency = $dependencyList->get($packageManagerName, $installedPackage->getName());

                if ($documentedDependency !== null && $documentedDependency->isVersionLocked()) {
                    $documentation[] = $this->createPackageLockedLine($installedPackage, $documentedDependency);
                } else {
                    $documentation[] = $this->createPackageLine($installedPackage);
                }

                $documentation[] = $this->createDescriptionLine($installedPackage->getDescription());

                if ($documentedDependency !== null) {
                    foreach ($documentedDependency->getAdditionalContent()->getAll() as $contentLine) {
                        $documentation[] = $contentLine;
                    }
                }
            }

            $documentation[] = "";
        }

        // @TODO: Maybe add documentation for packages which were documented but not installed (anymore)

        $documentation[] = "";

        file_put_contents($filepath, array_map(function ($line) {
            return $line . $this->configuration->getNewline();
        }, $documentation), LOCK_EX);
    }

    /**
     * @param string $packageManagerName
     * @return string
     */
    protected function createPackageManagerLine(string $packageManagerName): string
    {
        return "# $packageManagerName";
    }

    /**
     * @param PackageManagerPackageInterface $package
     * @param DependencyData $dependency
     * @return string
     */
    protected function createPackageLockedLine(
        PackageManagerPackageInterface $package,
        DependencyData $dependency
    ): string {
        $line = "## {$package->getName()} `{$package->getVersion()}` {$dependency->getLockSymbol()}";
        if ($this->getConfiguration()->isExportExternalLink()) {
            $line .= " {$this->createExternalLink($package)}";
        }

        return $line;
    }

    /**
     * @param PackageManagerPackageInterface $package
     * @return string
     */
    protected function createPackageLine(PackageManagerPackageInterface $package): string
    {
        $line = "## {$package->getName()} `{$package->getVersion()}`";
        if ($this->getConfiguration()->isExportExternalLink()) {
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
        return "[link]({$package->getExternalLink()})";
    }

    /**
     * @return WriterConfiguration
     */
    public function getConfiguration(): WriterConfiguration
    {
        return $this->configuration;
    }
}
