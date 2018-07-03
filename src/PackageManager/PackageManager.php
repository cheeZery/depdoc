<?php

namespace DepDoc\PackageManager;

abstract class PackageManager
{
    public function getName()
    {
        return end(explode('\\', get_called_class()));
    }

    abstract public function getInstalledPackages();
}