<?php
declare(strict_types=1);

namespace DepDoc\PackageManager\PackageList;

use DepDoc\PackageManager\Package\PackageManagerPackage;
use DepDoc\PackageManager\Package\PackageManagerPackageInterface;

class PackageManagerPackageList implements \ArrayAccess, \Countable, PackageManagerPackageListInterface
{
    /** @var PackageManagerPackage[][] */
    protected $dependencies = [];

    /**
     * @param PackageManagerPackageInterface $data
     * @return PackageManagerPackageList
     */
    public function add(PackageManagerPackageInterface $data): PackageManagerPackageListInterface
    {
        if ($this->offsetExists($data->getManagerName()) === false) {
            $this->dependencies[$data->getManagerName()] = [];
        }

        $this->dependencies[$data->getManagerName()][$data->getName()] = $data;

        return $this;
    }

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @return bool
     */
    public function has(string $packageManagerName, string $packageName): bool
    {
        if ($this->offsetExists($packageManagerName) === false) {
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
     * @return PackageManagerPackageListInterface[]
     */
    public function getAllFlat(): array
    {
        $allDependencies = [];
        array_walk($this->dependencies, function (array $packages) use (&$allDependencies) {
            $allDependencies = array_merge($allDependencies, $packages);
        });

        return $allDependencies;
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

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return isset($this->dependencies[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param string $offset <p>
     * The offset to retrieve.
     * </p>
     * @return PackageManagerPackage[]
     * @since 5.0.0
     */
    public function offsetGet($offset): array
    {
        return $this->dependencies[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param string $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param PackageManagerPackage $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value): void
    {
        throw new \RuntimeException(
            'Direct index modification is not allowed. Please use ' . __CLASS__ . '->add() oder merge().'
        );
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param string $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset): void
    {
        unset($this->dependencies[$offset]);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count(): int
    {
        return count($this->dependencies);
    }

    /**
     * @return int
     */
    public function countAll(): int
    {
        return count($this->getAllFlat());
    }
}
