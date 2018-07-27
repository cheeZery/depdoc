<?php
declare(strict_types=1);

namespace DepDocTest\PackageManager;

use PHPUnit\Framework\TestCase;

class AbstractPackageManagerTest extends TestCase
{
    public function testGetName()
    {
        $testDouble = new AbstractPackageManagerTestDouble();
        $this->assertEquals('AbstractPackageManagerTestDouble', $testDouble->getName());
    }
}
