<?php

namespace DepDoc\PackageManager;

abstract class AbstractPackageManager
{
    abstract public function getName();

    abstract public function getInstalledPackages(string $directory);
}
