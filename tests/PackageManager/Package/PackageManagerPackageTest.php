<?php

namespace DepDocTest\PackageManager\Package;

use DepDoc\PackageManager\Package\PackageManagerPackage;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PackageManagerPackageTest extends TestCase
{
    use ProphecyTrait;

    public function testToStringThrowsException()
    {
        $package = new PackageManagerPackage('Test', 'test/package', '1.0.0');
        self::assertEquals('[Test] test/package (1.0.0)', $package->__toString());
    }
}
