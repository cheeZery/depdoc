<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

abstract class PackageManager
{
    public function getName()
    {
        $fullyQualifiedClassNameParts = explode('\\', static::class);

        return end($fullyQualifiedClassNameParts);
    }

    abstract public function getInstalledPackages();
}
