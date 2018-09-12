<?php

namespace DepDocTest\Validator;

use DepDoc\Dependencies\DependencyData;
use DepDoc\PackageManager\Package\PackageManagerPackageInterface;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use DepDoc\Validator\PackageValidator;
use DepDoc\Validator\Result\ErrorDocumentedButNotInstalledResult;
use DepDoc\Validator\Result\ErrorMissingDocumentationResult;
use DepDoc\Validator\Result\ErrorVersionMissMatchResult;
use PHPUnit\Framework\TestCase;

class PackageValidatorTest extends TestCase
{

    public function testItComparesCorrectly()
    {
        $installedPackages = $this->prophesize(PackageManagerPackageList::class);
        $dependencyList = $this->prophesize(PackageManagerPackageList::class);

        $notExistingPackage = $this->getPackageProphecy('Composer', 'test1', '1.0.0');
        $versionMissMatchPackage = $this->getPackageProphecy('Composer', 'test2', '1.0.0');
        $versionMissMatchDependency = $this->getDependencyPackageProphecy('Composer', 'test2', '1.1.0', true);
        $notExistingDependency = $this->getDependencyPackageProphecy('Composer', 'test3', '1.0.0');

        $installedPackages->getAllFlat()->willReturn([
            $notExistingPackage->reveal(),
            $versionMissMatchPackage->reveal(),
        ])->shouldBeCalled();

        $dependencyList->has('Composer', 'test1')->willReturn(false)->shouldBeCalled();
        $dependencyList->has('Composer', 'test2')->willReturn(true)->shouldBeCalled();

        $dependencyList->get('Composer', 'test2')->willReturn($versionMissMatchDependency->reveal())->shouldBeCalled();

        $dependencyList->getAllFlat()->willReturn([
            $notExistingDependency->reveal(),
        ])->shouldBeCalled();

        $installedPackages->has('Composer', 'test3')->willReturn(false)->shouldBeCalled();

        $validator = new PackageValidator();
        $errorResultList = $validator->compare($installedPackages->reveal(), $dependencyList->reveal());

        $this->assertCount(3, $errorResultList);
        foreach ($errorResultList as $errorResult) {
            if ($errorResult instanceof ErrorMissingDocumentationResult) {
                $this->assertEquals($errorResult->getPackageName(), 'test1');
            } elseif ($errorResult instanceof ErrorVersionMissMatchResult) {
                $this->assertEquals($errorResult->getPackageName(), 'test2');
            } elseif ($errorResult instanceof ErrorDocumentedButNotInstalledResult) {
                $this->assertEquals($errorResult->getPackageName(), 'test3');
            } else {
                $this->fail('Unexpected error result: ' . get_class($errorResult));
            }
        }
    }

    /**
     * @param string $manager
     * @param string $name
     * @param string $version
     * @return PackageManagerPackageInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    protected function getPackageProphecy(string $manager, string $name, string $version)
    {
        $package = $this->prophesize(PackageManagerPackageInterface::class);

        $package->getManagerName()->willReturn($manager)->shouldBeCalled();
        $package->getName()->willReturn($name)->shouldBeCalled();
        $package->getVersion()->willReturn($version);

        return $package;
    }

    /**
     * @param string $manager
     * @param string $name
     * @param string $version
     * @param bool $isVersionLocked
     * @return DependencyData|\Prophecy\Prophecy\ObjectProphecy
     */
    protected function getDependencyPackageProphecy(
        string $manager,
        string $name,
        string $version,
        bool $isVersionLocked = false
    ) {
        $package = $this->prophesize(DependencyData::class);

        $package->getManagerName()->willReturn($manager);
        $package->getName()->willReturn($name);
        $package->getVersion()->willReturn($version);
        if ($isVersionLocked) {
            $package->getVersion()->shouldBeCalled();
            $package->isVersionLocked()->willReturn($isVersionLocked)->shouldBeCalled();
        }

        return $package;
    }
}
