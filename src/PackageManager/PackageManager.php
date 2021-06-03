<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

use DepDoc\Helper\CliCommandHelper;

abstract class PackageManager
{
    protected CliCommandHelper $cliCommandHelper;

    public function __construct(CliCommandHelper $cliCommandHelper)
    {
        $this->cliCommandHelper = $cliCommandHelper;
    }

    public function getName(): string
    {
        $fullyQualifiedClassNameParts = explode('\\', static::class);

        return end($fullyQualifiedClassNameParts);
    }

    abstract public function getInstalledPackages(): array;
}
