<?php

namespace DepDoc\PackageManager;

interface PackageManagerPackageInterface
{
    /**
     * @return string
     */
    public function getPackageManagerName(): string;

    /**
     * @return string
     */
    public function getPackageName(): string;

    /**
     * @return string
     */
    public function getVersion(): string;
}
