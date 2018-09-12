<?php
declare(strict_types=1);

namespace DepDoc\Writer;

use DepDoc\PackageManager\PackageList\PackageManagerPackageList;

interface WriterInterface
{
    /**
     * @param string $filepath
     * @param PackageManagerPackageList $installedPackages
     * @param PackageManagerPackageList $dependencyList
     * @return void
     */
    public function createDocumentation(
        string $filepath,
        PackageManagerPackageList $installedPackages,
        PackageManagerPackageList $dependencyList
    ): void;

    /**
     * @return WriterConfiguration
     */
    public function getConfiguration(): WriterConfiguration;
}
