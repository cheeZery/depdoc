<?php

namespace DepDocTest\PackageManager\PackageList;

use DepDoc\PackageManager\Package\PackageManagerPackageInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PackageManagerPackageListTest extends TestCase
{
    use ProphecyTrait;

    public function testItAddsPackage()
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

    public function testHas()
    {
        $package = $this->prophesize(PackageManagerPackageInterface::class);
        $package->getManagerName()->willReturn('Composer');
        $package->getName()->willReturn('test');

        $list = new PackageManagerPackageListTestDouble();
        $this->assertFalse($list->has('Composer', 'test'));

        $list->add($package->reveal());
        $this->assertTrue($list->has('Composer', 'test'));
    }

    public function testGet()
    {
        $package = $this->prophesize(PackageManagerPackageInterface::class);
        $package->getManagerName()->willReturn('Composer');
        $package->getName()->willReturn('test');

        $list = new PackageManagerPackageListTestDouble();
        $this->assertNull($list->get('Composer', 'test'));

        $list->add($package->reveal());
        $this->assertEquals($package->reveal(), $list->get('Composer', 'test'));
    }

    public function testGetAllReturnsMultiLevelArray()
    {
        $package1 = $this->prophesize(PackageManagerPackageInterface::class);
        $package1->getManagerName()->willReturn('Composer');
        $package1->getName()->willReturn('test1');
        $package2 = $this->prophesize(PackageManagerPackageInterface::class);
        $package2->getManagerName()->willReturn('Node');
        $package2->getName()->willReturn('test2');

        $list = new PackageManagerPackageListTestDouble();
        $this->assertEmpty($list->getAll());

        $list->add($package1->reveal());
        $list->add($package2->reveal());
        $this->assertEquals([
            'Composer' => ['test1' => $package1->reveal()],
            'Node' => ['test2' => $package2->reveal()],
        ], $list->getAll());
    }

    public function testGetAllFlatReturnsFlatArray()
    {
        $package1 = $this->prophesize(PackageManagerPackageInterface::class);
        $package1->getManagerName()->willReturn('Composer');
        $package1->getName()->willReturn('test1');
        $package2 = $this->prophesize(PackageManagerPackageInterface::class);
        $package2->getManagerName()->willReturn('Composer');
        $package2->getName()->willReturn('test2');
        $package3 = $this->prophesize(PackageManagerPackageInterface::class);
        $package3->getManagerName()->willReturn('Node');
        $package3->getName()->willReturn('test3');
        $package4 = $this->prophesize(PackageManagerPackageInterface::class);
        $package4->getManagerName()->willReturn('Node');
        $package4->getName()->willReturn('test2');
        $package5 = $this->prophesize(PackageManagerPackageInterface::class);
        $package5->getManagerName()->willReturn('Node');
        $package5->getName()->willReturn('test3');

        $list = new PackageManagerPackageListTestDouble();
        $this->assertEmpty($list->getAll());

        $list->add($package1->reveal());
        $list->add($package2->reveal());
        $list->add($package3->reveal());
        $list->add($package4->reveal());
        $list->add($package5->reveal());
        $this->assertEquals([
            $package1->reveal(),
            $package2->reveal(),
            $package5->reveal(),
            $package4->reveal(),
        ], $list->getAllFlat());
    }

    public function testGetAllByManager()
    {
        $package1 = $this->prophesize(PackageManagerPackageInterface::class);
        $package1->getManagerName()->willReturn('Composer');
        $package1->getName()->willReturn('test1');
        $package2 = $this->prophesize(PackageManagerPackageInterface::class);
        $package2->getManagerName()->willReturn('Node');
        $package2->getName()->willReturn('test2');

        $list = new PackageManagerPackageListTestDouble();
        $this->assertEmpty($list->getAllByManager('Composer'));
        $this->assertEmpty($list->getAllByManager('Node'));

        $list->add($package1->reveal());
        $this->assertEquals([
            'test1' => $package1->reveal()
        ], $list->getAllByManager('Composer'));
        $this->assertEmpty($list->getAllByManager('Node'));

        $list->add($package2->reveal());
        $this->assertEquals([
            'test2' => $package2->reveal()
        ], $list->getAllByManager('Node'));
    }
}
