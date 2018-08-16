<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

class PackageManagerPackageList implements \ArrayAccess, PackageManagerPackageListInterface
{
    /** @var PackageManagerPackage[] */
    protected $dependencies = [];

    /**
     * @param PackageManagerPackageInterface $data
     * @return PackageManagerPackageList
     */
    public function add(PackageManagerPackageInterface $data): PackageManagerPackageListInterface
    {
        $this[$this->getListKey($data->getPackageManagerName(), $data->getPackageName())] = $data;

        return $this;
    }

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @return bool
     */
    public function has(string $packageManagerName, string $packageName): bool
    {
        return $this->offsetExists($this->getListKey($packageManagerName, $packageName));
    }

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @return null|PackageManagerPackage
     */
    public function get(string $packageManagerName, string $packageName): ?PackageManagerPackageInterface
    {
        if (!$this->has($packageManagerName, $packageName)) {
            return null;
        }

        return $this[$this->getListKey($packageManagerName, $packageName)];
    }

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @return string
     */
    public function getListKey(string $packageManagerName, string $packageName): string
    {
        return sprintf('%s-%s', $packageManagerName, $packageName);
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
     * @return PackageManagerPackage
     * @since 5.0.0
     */
    public function offsetGet($offset): PackageManagerPackage
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
        $this->dependencies[$offset] = $value;
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
}
