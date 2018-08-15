<?php

namespace DepDoc\Validator\Result;

abstract class BaseResult
{
    /** @var string */
    protected $packageManagerName;
    /** @var string */
    protected $packageName;

    /**
     * @param string $packageManagerName
     * @param string $packageName
     */
    public function __construct(string $packageManagerName, string $packageName)
    {
        $this->packageManagerName = $packageManagerName;
        $this->packageName = $packageName;
    }

    /**
     * @return string
     */
    public function getPackageManagerName(): string
    {
        return $this->packageManagerName;
    }


    /**
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->packageName;
    }

    /**
     * @return string
     */
    abstract public function toString(): string;
}
