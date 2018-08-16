<?php

namespace DepDoc\PackageManager;

class ComposerPackage extends PackageManagerPackage
{
    /** @var string */
    protected $description;

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @param string $version
     * @param string $description
     */
    public function __construct(
        string $packageManagerName,
        string $packageName,
        string $version,
        string $description
    ) {
        parent::__construct($packageManagerName, $packageName, $version);

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
