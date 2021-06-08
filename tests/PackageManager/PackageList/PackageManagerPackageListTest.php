<?php

namespace DepDocTest\PackageManager\PackageList;

use DepDoc\PackageManager\Package\PackageManagerPackage;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PackageManagerPackageListTest extends TestCase
{
    use ProphecyTrait;

    public function testItAddsPackage(): void
    {
        $package = new PackageManagerPackage('Composer', 'test', '1');

        $list = new PackageManagerPackageListTestDouble();

        // Will create an empty array for cached dependencies
        self::assertNull($list->getCachedFlatDependencies());
        $list->getAllFlat();
        self::assertNotNull($list->getCachedFlatDependencies());

        $list->add($package);
        self::assertNull($list->getCachedFlatDependencies());
        self::assertEquals(['Composer' => ['test' => $package]], $list->getDependencies());
    }

    public function testHas(): void
    {
        $package = new PackageManagerPackage('Composer', 'test', '1');

        $list = new PackageManagerPackageListTestDouble();
        self::assertFalse($list->has('Composer', 'test'));

        $list->add($package);
        self::assertTrue($list->has('Composer', 'test'));
    }

    public function testGet(): void
    {
        $package = new PackageManagerPackage('Composer', 'test', '1');

        $list = new PackageManagerPackageListTestDouble();
        self::assertNull($list->get('Composer', 'test'));

        $list->add($package);
        self::assertEquals($package, $list->get('Composer', 'test'));
    }

    public function testGetAllReturnsMultiLevelArray(): void
    {
        $package1 = new PackageManagerPackage('Composer', 'test1', '1');
        $package2 = new PackageManagerPackage('Node', 'test2', '1');

        $list = new PackageManagerPackageListTestDouble();
        self::assertEmpty($list->getAll());

        $list->add($package1);
        $list->add($package2);
        self::assertEquals([
            'Composer' => ['test1' => $package1],
            'Node' => ['test2' => $package2],
        ], $list->getAll());
    }

    public function testGetAllFlatReturnsFlatArray(): void
    {
        $package1 = new PackageManagerPackage('Composer', 'test1', '1');
        $package2 = new PackageManagerPackage('Composer', 'test2', '1');
        $package3 = new PackageManagerPackage('Node', 'test3', '1');
        $package4 = new PackageManagerPackage('Node', 'test2', '1');
        $package5 = new PackageManagerPackage('Node', 'test3', '1');

        $list = new PackageManagerPackageListTestDouble();
        self::assertEmpty($list->getAll());

        $list->add($package1);
        $list->add($package2);
        $list->add($package3);
        $list->add($package4);
        $list->add($package5);
        self::assertEquals([
            $package1,
            $package2,
            $package5,
            $package4,
        ], $list->getAllFlat());
    }

    public function testGetAllByManager(): void
    {
        $package1 = new PackageManagerPackage('Composer', 'test1', '1');
        $package2 = new PackageManagerPackage('Node', 'test2', '1');

        $list = new PackageManagerPackageListTestDouble();
        self::assertEmpty($list->getAllByManager('Composer'));
        self::assertEmpty($list->getAllByManager('Node'));

        $list->add($package1);
        self::assertEquals([
            'test1' => $package1
        ], $list->getAllByManager('Composer'));
        self::assertEmpty($list->getAllByManager('Node'));

        $list->add($package2);
        self::assertEquals([
            'test2' => $package2
        ], $list->getAllByManager('Node'));
    }
}
