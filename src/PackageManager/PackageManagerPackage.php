<?php

namespace DepDoc\PackageManager;

class PackageManagerPackage implements PackageManagerPackageInterface
{
    /** @var string */
    protected $packageManagerName;
    /** @var string */
    protected $packageName;
    /** @var string */
    protected $version;

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @param string $version
     */
    public function __construct(
        string $packageManagerName,
        string $packageName,
        string $version
    ) {
        $this->packageManagerName = $packageManagerName;
        $this->packageName = $packageName;
        $this->version = $version;
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
    public function getVersion(): string
    {
        return $this->version;
    }
}
