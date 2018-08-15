<?php

namespace DepDoc\Dependencies;

class DependencyData
{
    /** @var string */
    protected $packageManagerName;
    /** @var string */
    protected $packageName;
    /** @var string */
    protected $versionLockSymbol;
    /** @var bool */
    protected $isVersionLocked;
    /** @var DependencyDataAdditionalContent */
    protected $additionalContent;

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @param string $versionLockSymbol
     * @param bool $isVersionLocked
     * @param array $additionalContent
     */
    public function __construct(
        string $packageManagerName,
        string $packageName,
        string $versionLockSymbol,
        bool $isVersionLocked,
        array $additionalContent = []
    ) {
        $this->packageManagerName = $packageManagerName;
        $this->packageName = $packageName;
        $this->versionLockSymbol = $versionLockSymbol;
        $this->isVersionLocked = $isVersionLocked;
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
     * @return string
     */
    public function getVersionLockSymbol(): string
    {
        return $this->versionLockSymbol;
    }

    /**
     * @return bool
     */
    public function isVersionLocked(): bool
    {
        return $this->isVersionLocked;
    }

    /**
     * @return DependencyDataAdditionalContent
     */
    public function getAdditionalContent(): DependencyDataAdditionalContent
    {
        return $this->additionalContent;
    }
}
