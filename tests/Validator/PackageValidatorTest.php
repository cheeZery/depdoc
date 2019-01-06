<?php

namespace DepDocTest\Validator;

use DepDoc\Dependencies\DependencyData;
use DepDoc\PackageManager\Package\PackageManagerPackageInterface;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use DepDoc\Validator\PackageValidator;
use DepDoc\Validator\Result\ErrorDocumentedButNotInstalledResult;
use DepDoc\Validator\Result\ErrorMissingDocumentationResult;
use DepDoc\Validator\Result\ErrorVersionMisMatchResult;
use DepDoc\Validator\StrictMode;
use PHPUnit\Framework\TestCase;

class PackageValidatorTest extends TestCase
{
    public function testItComparesCorrectlyForExistingOrLocked()
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
        $errorResultList = $validator->compare(
            StrictMode::existingOrLocked(),
            $installedPackages->reveal(),
            $dependencyList->reveal()
        );

        $this->assertCount(3, $errorResultList);
        foreach ($errorResultList as $errorResult) {
            if ($errorResult instanceof ErrorMissingDocumentationResult) {
                $this->assertEquals($errorResult->getPackageName(), 'test1');
            } elseif ($errorResult instanceof ErrorVersionMisMatchResult) {
                $this->assertEquals($errorResult->getPackageName(), 'test2');
            } elseif ($errorResult instanceof ErrorDocumentedButNotInstalledResult) {
                $this->assertEquals($errorResult->getPackageName(), 'test3');
            } else {
                $this->fail('Unexpected error result: ' . get_class($errorResult));
            }
        }
    }

    public function testItComparesCorrectlyForMajorAndMinor()
    {
        $installedPackages = $this->prophesize(PackageManagerPackageList::class);
        $dependencyList = $this->prophesize(PackageManagerPackageList::class);

        $versionMissMatchPackage = $this->getPackageProphecy('Composer', 'test2', '1.0.0');
        $versionMissMatchDependency = $this->getDependencyPackageProphecy('Composer', 'test2', '1.1.0');

        $versionMissMatchPatchPackage = $this->getPackageProphecy('Composer', 'test1', '1.0.0');
        $versionMissMatchPatchDependency = $this->getDependencyPackageProphecy('Composer', 'test1', '1.0.1');

        $installedPackages->has('Composer', 'test1')->willReturn(true)->shouldBeCalled();
        $installedPackages->has('Composer', 'test2')->willReturn(true)->shouldBeCalled();

        $installedPackages->getAllFlat()->willReturn([
            $versionMissMatchPatchPackage->reveal(),
            $versionMissMatchPackage->reveal(),
        ])->shouldBeCalled();

        $dependencyList->has('Composer', 'test1')->willReturn(true)->shouldBeCalled();
        $dependencyList->has('Composer', 'test2')->willReturn(true)->shouldBeCalled();

        $dependencyList->get('Composer', 'test1')->willReturn($versionMissMatchPatchDependency->reveal())->shouldBeCalled();
        $dependencyList->get('Composer', 'test2')->willReturn($versionMissMatchDependency->reveal())->shouldBeCalled();

        $dependencyList->getAllFlat()->willReturn([
              $versionMissMatchDependency->reveal(),
              $versionMissMatchPatchDependency->reveal(),
        ])->shouldBeCalled();

        $validator = new PackageValidator();
        $errorResultList = $validator->compare(
            StrictMode::majorAndMinor(),
            $installedPackages->reveal(),
            $dependencyList->reveal()
        );

        $this->assertCount(1, $errorResultList);
        foreach ($errorResultList as $errorResult) {
            if ($errorResult instanceof ErrorVersionMisMatchResult) {
                $this->assertEquals($errorResult->getPackageName(), 'test2');
            } else {
                $this->fail('Unexpected error result: ' . get_class($errorResult));
            }
        }
    }


    public function testItComparesCorrectlyForFullSemanticVersion()
    {
        $installedPackages = $this->prophesize(PackageManagerPackageList::class);
        $dependencyList = $this->prophesize(PackageManagerPackageList::class);

        $versionMatchingPackage = $this->getPackageProphecy('Composer', 'test2', '1.0.0');
        $versionMatchingDependency = $this->getDependencyPackageProphecy('Composer', 'test2', '1.0.0');

        $versionMissMatchPatchPackage = $this->getPackageProphecy('Composer', 'test1', '1.0.0');
        $versionMissMatchPatchDependency = $this->getDependencyPackageProphecy('Composer', 'test1', '1.0.1');

        $installedPackages->has('Composer', 'test1')->willReturn(true)->shouldBeCalled();
        $installedPackages->has('Composer', 'test2')->willReturn(true)->shouldBeCalled();

        $installedPackages->getAllFlat()->willReturn([
            $versionMissMatchPatchPackage->reveal(),
            $versionMatchingPackage->reveal(),
        ])->shouldBeCalled();

        $dependencyList->has('Composer', 'test1')->willReturn(true)->shouldBeCalled();
        $dependencyList->has('Composer', 'test2')->willReturn(true)->shouldBeCalled();

        $dependencyList->get('Composer', 'test1')->willReturn($versionMissMatchPatchDependency->reveal())->shouldBeCalled();
        $dependencyList->get('Composer', 'test2')->willReturn($versionMatchingDependency->reveal())->shouldBeCalled();

        $dependencyList->getAllFlat()->willReturn([
            $versionMatchingDependency->reveal(),
            $versionMissMatchPatchDependency->reveal(),
        ])->shouldBeCalled();

        $validator = new PackageValidator();
        $errorResultList = $validator->compare(
            StrictMode::fullSemVerMatch(),
            $installedPackages->reveal(),
            $dependencyList->reveal()
        );

        $this->assertCount(1, $errorResultList);
        foreach ($errorResultList as $errorResult) {
            if ($errorResult instanceof ErrorVersionMisMatchResult) {
                $this->assertEquals($errorResult->getPackageName(), 'test1');
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
