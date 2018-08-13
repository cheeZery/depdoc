<?php
declare(strict_types=1);

namespace DepDocTest\PackageManager;

use DepDoc\PackageManager\AbstractPackageManager;

class AbstractPackageManagerTestDouble extends AbstractPackageManager
{
    public function getName()
    {
        return 'Test';
    }

    public function getInstalledPackages(string $directory)
    {

    }
}
