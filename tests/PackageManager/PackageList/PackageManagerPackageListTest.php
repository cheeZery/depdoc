<?php

namespace DepDocTest\PackageManager\PackageList;

use DepDoc\PackageManager\Package\PackageManagerPackageInterface;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;

use PHPUnit\Framework\TestCase;

class PackageManagerPackageListTest extends TestCase
{
    public function testAdd()
    {
        $package = $this->prophesize(PackageManagerPackageInterface::class);
        $package->getManagerName()->willReturn('Composer');
        $package->getName()->willReturn('test');

        $list = new PackageManagerPackageListTestDouble();

        // Will create an empty array for cached dependencies
        $this->assertNull($list->getCachedFlatDependencies());
        $list->getAllFlat();
        $this->assertNotNull($list->getCachedFlatDependencies());

        $list->add($package->reveal());
        $this->assertNull($list->getCachedFlatDependencies());
        $this->assertEquals(['Composer' => ['test' => $package->reveal()]], $list->getDependencies());
    }
}
