<?php
declare(strict_types=1);

namespace DepDoc\Parser;

use DepDoc\PackageManager\PackageList\PackageManagerPackageList;

interface ParserInterface
{
    public function getDocumentedDependencies(
        string $filepath,
        ?string $packageManagerName = null
    ): PackageManagerPackageList;
}
