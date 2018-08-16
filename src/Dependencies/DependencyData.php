<?php

namespace DepDoc\Dependencies;

class DependencyData
{
    /** @var string */
    protected $packageManagerName;
    /** @var string */
    protected $packageName;
    /** @var null|string */
    protected $lockSymbol;
    /** @var DependencyDataAdditionalContent */
    protected $additionalContent;
    /**
     * @var string
     */
    protected $version;

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @param string $version
     * @param null|string $lockSymbol
     * @param array $additionalContent
     */
    public function __construct(
        string $packageManagerName,
        string $packageName,
        string $version,
        string $lockSymbol,
        array $additionalContent = []
    ) {
        $this->packageManagerName = $packageManagerName;
        $this->packageName = $packageName;
        $this->version = $version;
        $this->lockSymbol = $lockSymbol;
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
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return null|string
     */
    public function getLockSymbol(): ?string
    {
        return $this->lockSymbol;
    }

    /**
     * @return bool
     */
    public function isVersionLocked(): bool
    {
        return $this->getLockSymbol() !== null;
    }

    /**
     * @return DependencyDataAdditionalContent
     */
    public function getAdditionalContent(): DependencyDataAdditionalContent
    {
        return $this->additionalContent;
    }
}
