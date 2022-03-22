<?php
declare(strict_types=1);

namespace DepDoc\PackageManager\PackageList;

use DepDoc\PackageManager\Package\PackageManagerPackageInterface;

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
     * @return null|PackageManagerPackageInterface
     */
    public function get(string $packageManagerName, string $packageName): ?PackageManagerPackageInterface;

    /**
     * @return PackageManagerPackageInterface[]
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
