<?php
declare(strict_types=1);

namespace DepDoc\Dependencies;

use DepDoc\PackageManager\PackageManagerPackage;

class DependencyData extends PackageManagerPackage
{
    /** @var null|string */
    protected $lockSymbol;
    /** @var DependencyDataAdditionalContent */
    protected $additionalContent;

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
        ?string $lockSymbol,
        array $additionalContent = []
    ) {
        parent::__construct($packageManagerName, $packageName, $version);

        $this->lockSymbol = $lockSymbol;
        $this->additionalContent = new DependencyDataAdditionalContent($additionalContent ?? []);
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
