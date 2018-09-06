<?php
declare(strict_types=1);

namespace DepDocTest\PackageManager\PackageList;

use DepDoc\PackageManager\Package\PackageManagerPackage;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;

class PackageManagerPackageListTestDouble extends PackageManagerPackageList
{
    /**
     * @return \DepDoc\PackageManager\Package\PackageManagerPackage[][]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @return PackageManagerPackage[]|null
     */
    public function getCachedFlatDependencies(): ?array
    {
        return $this->cachedFlatDependencies;
    }
}
