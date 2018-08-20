<?php

namespace DepDoc\PackageManager\Package;

class ComposerPackage extends PackageManagerPackage
{
    /** @var string */
    protected $description;

    /**
     * @param string $managerName
     * @param string $name
     * @param string $version
     * @param string $description
     */
    public function __construct(string $managerName, string $name, string $version, ?string $description)
    {
        parent::__construct($managerName, $name, $version);

        $this->description = $description;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
