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
     * @return PackageManagerPackageListInterface[][]
     */
    public function getAllFlat(): array;

    /**
     * @param string $manager
     * @return PackageManagerPackageInterface[]
     */
    public function getAllByManager(string $manager): array;

    /**
     * @param PackageManagerPackageListInterface $packageList
     * @return PackageManagerPackageListInterface
     */
    public function merge(PackageManagerPackageListInterface $packageList): PackageManagerPackageListInterface;
}
