<?php

namespace DepDoc\Dependencies;

class DependencyList implements \ArrayAccess
{
    /** @var DependencyData[] */
    protected $dependencies = [];

    /**
     * @param DependencyData $data
     * @return DependencyList
     */
    public function add(DependencyData $data): DependencyList
    {
        $this[$this->getListKey($data)] = $data;

        return $this;
    }

    /**
     * @param DependencyData $data
     * @return string
     */
    public function getListKey(DependencyData $data): string
    {
        return sprintf('%s-%s', $data->getPackageManagerName(), $data->getPackageName());
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
    public function offsetExists($offset)
    {
        return isset($this->dependencies[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param string $offset <p>
     * The offset to retrieve.
     * </p>
     * @return DependencyData
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->dependencies[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param string $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param DependencyData $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
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
    public function offsetUnset($offset)
    {
        unset($this->dependencies[$offset]);
    }
}
