<?php
declare(strict_types=1);

namespace DepDoc\PackageManager\Package;

class PackageManagerPackage implements PackageManagerPackageInterface
{
    /** @var string */
    protected $managerName;
    /** @var string */
    protected $name;
    /** @var string */
    protected $version;

    /**
     * @param string $managerName
     * @param string $name
     * @param string $version
     */
    public function __construct(string $managerName, string $name, string $version)
    {
        $this->managerName = $managerName;
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getManagerName(): string
    {
        return $this->managerName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
