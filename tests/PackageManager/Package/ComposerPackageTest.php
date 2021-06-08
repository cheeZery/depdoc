<?php

namespace DepDocTest\PackageManager\Package;

use DepDoc\PackageManager\Package\ComposerPackage;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ComposerPackageTest extends TestCase
{
    use ProphecyTrait;

    public function testItBuildsCorrectExternalLInk()
    {
        $package = new ComposerPackage('Composer', 'test/package', '1.0.0', null);
        self::assertEquals('https://packagist.org/packages/test/package', $package->getExternalLink());
    }

    public function testItReturnsAnInformativeString()
    {
        $package = new ComposerPackage('Composer', 'test/package', '1.0.0', null);
        self::assertEquals(
            '[Composer] test/package (1.0.0 / https://packagist.org/packages/test/package)',
            $package->__toString()
        );
    }
}
