<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

use DepDoc\PackageManager\PackageList\PackageManagerPackageList;

interface PackageManagerInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $directory
     * @return PackageManagerPackageList
     */
    public function getInstalledPackages(string $directory): PackageManagerPackageList;
}
