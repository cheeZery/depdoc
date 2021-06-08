<?php
declare(strict_types=1);

namespace DepDoc\PackageManager\PackageList;

use DepDoc\PackageManager\Package\PackageManagerPackage;
use DepDoc\PackageManager\Package\PackageManagerPackageInterface;

class PackageManagerPackageList implements PackageManagerPackageListInterface
{
    /** @var PackageManagerPackage[][] */
    protected array $dependencies = [];
    /** @var null|PackageManagerPackage[] */
    protected ?array $cachedFlatDependencies;

    public function add(PackageManagerPackageInterface $data): PackageManagerPackageListInterface
    {
        if (isset($this->dependencies[$data->getManagerName()]) === false) {
            $this->dependencies[$data->getManagerName()] = [];
        }

        // @TODO: Check for same package name and throw exception in case somebody edits the file manually
        $this->dependencies[$data->getManagerName()][$data->getName()] = $data;
        $this->cachedFlatDependencies = null;

        return $this;
    }

    public function has(string $packageManagerName, string $packageName): bool
    {
        if (isset($this->dependencies[$packageManagerName]) === false) {
            return false;
        }

        return array_key_exists($packageName, $this->getAllByManager($packageManagerName));
    }

    public function get(string $packageManagerName, string $packageName): ?PackageManagerPackage
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
        if ($this->cachedFlatDependencies !== null) {
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
     * @return PackageManagerPackage[]
     */
    public function getAllByManager(string $manager): array
    {
        if (array_key_exists($manager, $this->dependencies) === false) {
            return [];
        }

        return $this->dependencies[$manager];
    }

    public function merge(PackageManagerPackageListInterface $packageList): PackageManagerPackageListInterface
    {
        foreach ($packageList->getAllFlat() as $package) {
            $this->add(clone $package);
        }

        return $this;
    }
}
