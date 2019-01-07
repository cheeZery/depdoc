<?php
declare(strict_types=1);

namespace DepDoc\PackageManager\Package;

class ComposerPackage extends PackageManagerPackage
{
    /** @var string|null */
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
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getExternalLink(): string
    {
        return sprintf('https://packagist.org/packages/%s', $this->getName());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '[%s] %s (%s / %s)',
            $this->getManagerName(),
            $this->getName(),
            $this->getVersion(),
            $this->getExternalLink()
        );
    }
}
