<?php
declare(strict_types=1);

namespace DepDoc\Writer;

use DepDoc\PackageManager\PackageManagerPackageList;

interface WriterInterface
{
    public function createDocumentation(
        string $filepath,
        array $installedPackages,
        PackageManagerPackageList $dependencyList,
        WriterConfiguration $configuration
    );
}
