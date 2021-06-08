<?php
declare(strict_types=1);

namespace DepDoc\PackageManager\Package;

class PackageManagerPackage implements PackageManagerPackageInterface
{
    protected string $managerName;
    protected string $name;
    protected string $version;

    public function __construct(string $managerName, string $name, string $version)
    {
        $this->managerName = $managerName;
        $this->name = $name;
        $this->version = $version;
    }

    public function getManagerName(): string
    {
        return $this->managerName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getExternalLink(): string
    {
        throw new \RuntimeException(__CLASS__ . ' does not have an external url');
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s] %s (%s)',
            $this->getManagerName(),
            $this->getName(),
            $this->getVersion()
        );
    }
}
