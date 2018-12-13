<?php

declare(strict_types=1);

namespace DepDoc\PackageManager\Factory;

use Composer\Factory;
use Composer\IO\NullIO;
use DepDoc\PackageManager\ComposerPackageManager;

/**
 * @package DepDoc\PackageManager\Factory
 */
class ComposerPackageManagerFactory
{
    public static function create(): ComposerPackageManager
    {
        // Find a better way to create composer package with ConsoleIO.

        return new ComposerPackageManager(
            Factory::create(new NullIO())
        );
    }
}
