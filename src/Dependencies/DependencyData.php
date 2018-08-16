<?php

namespace DepDoc\Dependencies;

class DependencyData
{
    /** @var string */
    protected $packageManagerName;
    /** @var string */
    protected $packageName;
    /** @var null|string */
    protected $versionLockSymbol;
    /** @var DependencyDataAdditionalContent */
    protected $additionalContent;

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @param null|string $versionLockSymbol
     * @param array $additionalContent
     */
    public function __construct(
        string $packageManagerName,
        string $packageName,
        string $versionLockSymbol,
        array $additionalContent = []
    ) {
        $this->packageManagerName = $packageManagerName;
        $this->packageName = $packageName;
        $this->versionLockSymbol = $versionLockSymbol;
        $this->additionalContent = new DependencyDataAdditionalContent($additionalContent ?? []);
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
     * @return null|string
     */
    public function getVersionLockSymbol(): ?string
    {
        return $this->versionLockSymbol;
    }

    /**
     * @return bool
     */
    public function isVersionLocked(): bool
    {
        return $this->getVersionLockSymbol() !== null;
    }

    /**
     * @return DependencyDataAdditionalContent
     */
    public function getAdditionalContent(): DependencyDataAdditionalContent
    {
        return $this->additionalContent;
    }
}
