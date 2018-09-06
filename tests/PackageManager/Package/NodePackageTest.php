<?php

namespace DepDocTest\PackageManager\Package;

use DepDoc\PackageManager\Package\NodePackage;
use PHPUnit\Framework\TestCase;

class NodePackageTest extends TestCase
{
    public function testItBuildsCorrectExternalLInk()
    {
        $package = new NodePackage('Node', 'test/package', '1.0.0', null);
        $this->assertEquals('https://www.npmjs.com/package/test/package', $package->getExternalLink());
    }

    public function testItReturnsAnInformativeString()
    {
        $package = new NodePackage('Node', 'test/package', '1.0.0', null);
        $this->assertEquals(
            '[Node] test/package (1.0.0 / https://www.npmjs.com/package/test/package)',
            $package->__toString()
        );
    }
}
