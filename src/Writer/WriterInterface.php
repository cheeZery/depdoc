<?php
declare(strict_types=1);

namespace DepDoc\Writer;

use DepDoc\PackageManager\PackageList\PackageManagerPackageList;

interface WriterInterface
{
    public function createDocumentation(
        string $filepath,
        PackageManagerPackageList $installedPackages,
        PackageManagerPackageList $dependencyList,
        WriterConfiguration $configuration
    );
}
