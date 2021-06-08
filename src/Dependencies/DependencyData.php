<?php
declare(strict_types=1);

namespace DepDoc\Dependencies;

use DepDoc\PackageManager\Package\PackageManagerPackage;

/**
 * @codeCoverageIgnore
 */
class DependencyData extends PackageManagerPackage
{
    protected ?string $lockSymbol;
    protected DependencyDataAdditionalContent $additionalContent;

    /**
     * @param string $managerName
     * @param string $name
     * @param string $version
     * @param null|string $lockSymbol
     * @param string[] $additionalContent
     */
    public function __construct(
        string $managerName,
        string $name,
        string $version,
        ?string $lockSymbol,
        array $additionalContent = []
    ) {
        parent::__construct($managerName, $name, $version);

        $this->lockSymbol = $lockSymbol;
        $this->additionalContent = new DependencyDataAdditionalContent($additionalContent ?? []);
    }

    public function getLockSymbol(): ?string
    {
        return $this->lockSymbol;
    }

    public function isVersionLocked(): bool
    {
        return $this->getLockSymbol() !== null;
    }

    public function getAdditionalContent(): DependencyDataAdditionalContent
    {
        return $this->additionalContent;
    }
}
