<?php

namespace DepDoc\PackageManager;

interface PackageManagerPackageListInterface
{
    /**
     * @param PackageManagerPackageInterface $data
     * @return PackageManagerPackageList
     */
    public function add(PackageManagerPackageInterface $data): PackageManagerPackageListInterface;

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @return bool
     */
    public function has(string $packageManagerName, string $packageName): bool;

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @return null|PackageManagerPackage
     */
    public function get(string $packageManagerName, string $packageName): ?PackageManagerPackageInterface;

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @return string
     */
    public function getListKey(string $packageManagerName, string $packageName): string;
}
