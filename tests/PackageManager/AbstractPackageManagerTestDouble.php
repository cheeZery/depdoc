<?php
declare(strict_types=1);

namespace DepDocTest\PackageManager;

use DepDoc\PackageManager\PackageManagerInterface;

class AbstractPackageManagerTestDouble extends PackageManagerInterface
{
    public function getName()
    {
        return 'Test';
    }

    public function getInstalledPackages(string $directory)
    {

    }
}
