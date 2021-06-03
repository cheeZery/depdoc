<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

abstract class PackageManager
{
    public function getName()
    {
        $fullyQualifiedClassNameParts = explode('\\', get_called_class());

        return end($fullyQualifiedClassNameParts);
    }

    abstract public function getInstalledPackages();
}
