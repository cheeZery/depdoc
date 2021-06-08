<?php

declare(strict_types=1);

namespace DepDoc\PackageManager\Factory;

use Composer\Factory;
use Composer\IO\NullIO;
use DepDoc\PackageManager\ComposerPackageManager;

class ComposerPackageManagerFactory
{
    public static function create(Factory $factory = null): ComposerPackageManager
    {
        // Find a better way to create composer package with ConsoleIO.
        $factory = $factory ?? new Factory();

        return new ComposerPackageManager(
            $factory->createComposer(new NullIO())
        );
    }
}
