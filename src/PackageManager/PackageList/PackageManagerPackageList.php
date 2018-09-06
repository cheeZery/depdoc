<?php
declare(strict_types=1);

namespace DepDoc\PackageManager\PackageList;

use DepDoc\PackageManager\Package\PackageManagerPackage;
use DepDoc\PackageManager\Package\PackageManagerPackageInterface;

class PackageManagerPackageList implements PackageManagerPackageListInterface
{
    /** @var PackageManagerPackage[][] */
    protected $dependencies = [];
    /** @var null|PackageManagerPackage[] */
    protected $cachedFlatDependencies;

    /**
     * @param PackageManagerPackageInterface $data
     * @return PackageManagerPackageList
     */
    public function add(PackageManagerPackageInterface $data): PackageManagerPackageListInterface
    {
        if (isset($this->dependencies[$data->getManagerName()]) === false) {
            $this->dependencies[$data->getManagerName()] = [];
        }

        $this->dependencies[$data->getManagerName()][$data->getName()] = $data;
        $this->cachedFlatDependencies = null;

        return $this;
    }

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @return bool
     */
    public function has(string $packageManagerName, string $packageName): bool
    {
        if (isset($this->dependencies[$packageManagerName]) === false) {
            return false;
        }

        return array_key_exists($packageName, $this->getAllByManager($packageManagerName));
    }

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @return null|PackageManagerPackage
     */
    public function get(string $packageManagerName, string $packageName): ?PackageManagerPackageInterface
    {
        if ($this->has($packageManagerName, $packageName) === false) {
            return null;
        }

        return $this->getAllByManager($packageManagerName)[$packageName];
    }

    /**
     * @return PackageManagerPackageInterface[][]
     */
    public function getAll(): array
    {
        return $this->dependencies;
    }

    /**
     * @return PackageManagerPackageInterface[]
     */
    public function getAllFlat(): array
    {
        if ($this->cachedFlatDependencies) {
            return $this->cachedFlatDependencies;
        }

        $this->cachedFlatDependencies = [];
        foreach ($this->dependencies as $managerDependencies) {
            $this->cachedFlatDependencies = array_merge(
                $this->cachedFlatDependencies,
                array_values($managerDependencies)
            );
        }

        return $this->cachedFlatDependencies;
    }

    /**
     * @param string $manager
     * @return PackageManagerPackageInterface[]
     */
    public function getAllByManager(string $manager): array
    {
        if (array_key_exists($manager, $this->dependencies) === false) {
            return [];
        }

        return $this->dependencies[$manager];
    }

    /**
     * @param PackageManagerPackageListInterface $packageList
     * @return PackageManagerPackageListInterface
     */
    public function merge(PackageManagerPackageListInterface $packageList): PackageManagerPackageListInterface
    {
        foreach ($packageList->getAllFlat() as $package) {
            $this->add(clone $package);
        }

        return $this;
    }
}
