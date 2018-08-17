<?php
declare(strict_types=1);

namespace DepDoc\PackageManager\Package;

class NodePackage extends PackageManagerPackage
{
    /** @var string */
    protected $description;

    /**
     * @param string $managerName
     * @param string $name
     * @param string $version
     * @param null|string $description
     */
    public function __construct(string $managerName, string $name, string $version, ?string $description)
    {
        parent::__construct($managerName, $name, $version);

        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
