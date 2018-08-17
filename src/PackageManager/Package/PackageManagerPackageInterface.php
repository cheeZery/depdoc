<?php
declare(strict_types=1);

namespace DepDoc\PackageManager\Package;

interface PackageManagerPackageInterface
{
    /**
     * @return string
     */
    public function getManagerName(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getVersion(): string;
}
