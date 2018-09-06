<?php

namespace DepDocTest\PackageManager\Package;

use DepDoc\PackageManager\Package\PackageManagerPackage;

use PHPUnit\Framework\TestCase;

class PackageManagerPackageTest extends TestCase
{

    public function testToStringThrowsException()
    {
        $package = new PackageManagerPackage('Test', 'test/package', '1.0.0');
        $this->assertEquals('[Test] test/package (1.0.0)', $package->__toString());
    }
}
