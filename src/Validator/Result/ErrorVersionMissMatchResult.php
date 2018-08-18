<?php

namespace DepDoc\Validator\Result;

class ErrorVersionMissMatchResult extends AbstractErrorResult
{
    /** @var string */
    protected $installedVersion;
    /** @var null|string */
    protected $lockVersion;

    /**
     * @param string $packageManagerName
     * @param string $packageName
     * @param string $installedVersion
     * @param null|string $lockVersion
     */
    public function __construct(
        string $packageManagerName,
        string $packageName,
        string $installedVersion,
        ?string $lockVersion
    ) {
        parent::__construct($packageManagerName, $packageName);

        $this->installedVersion = $installedVersion;
        $this->lockVersion = $lockVersion;
    }

    /**
     * @return string
     */
    public function getInstalledVersion(): string
    {
        return $this->installedVersion;
    }

    /**
     * @return null|string
     */
    public function getLockVersion(): ?string
    {
        return $this->lockVersion;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return sprintf(
            "[%s] package %s installed in version %s but locked for %s!",
            $this->getPackageManagerName(),
            $this->getPackageName(),
            $this->getInstalledVersion(),
            $this->getLockVersion()
        );
    }
}
