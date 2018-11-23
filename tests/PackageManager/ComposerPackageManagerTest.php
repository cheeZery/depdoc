<?php

namespace DepDocTest\PackageManager;

use Composer\Composer;
use Composer\Package\CompletePackage;
use Composer\Package\Link;
use Composer\Package\Locker;
use Composer\Package\RootPackage;
use Composer\Repository\RepositoryInterface;
use Composer\Repository\RepositoryManager;
use Composer\Semver\Constraint\ConstraintInterface;
use DepDoc\PackageManager\ComposerPackageManager;
use PHPUnit\Framework\TestCase;

class ComposerPackageManagerTest extends TestCase
{
    public function testGetInstalledPackages()
    {
        $package = $this->prophesize(CompletePackage::class);
        $package->getName()->willReturn('test/test')->shouldBeCalled();
        $package->getPrettyVersion()->willReturn('1.0.0')->shouldBeCalled();
        $package->getDescription()->willReturn('test package')->shouldBeCalled(
        );

        $contraint = $this->prophesize(ConstraintInterface::class);

        $link = $this->prophesize(Link::class);
        $link->getTarget()->willReturn('test/test')->shouldBeCalled();
        $link->getConstraint()->willReturn(
            $contraint->reveal()
        )->shouldBeCalled();

        $rootPackage = $this->prophesize(RootPackage::class);
        $rootPackage->getRequires()->willReturn(
            [ $link->reveal() ]
        )->shouldBeCalled();
        $rootPackage->getDevRequires()->willReturn([])->shouldBeCalled();

        $localRepository = $this->prophesize(RepositoryInterface::class);
        $localRepository
            ->findPackage('test/test', $contraint->reveal())
            ->willReturn($package->reveal())
            ->shouldBeCalled();

        $repositoryManager = $this->prophesize(RepositoryManager::class);
        $repositoryManager->getLocalRepository()->willReturn(
            $localRepository->reveal()
        )->shouldBeCalled();

        $lockedRepository = $this->prophesize(RepositoryInterface::class);
        $lockedRepository
            ->findPackage('test/test', $contraint->reveal())
            ->willReturn($package->reveal())
            ->shouldBeCalled();

        $locker = $this->prophesize(Locker::class);
        $locker->getLockedRepository(true)->willReturn(
            $lockedRepository->reveal()
        )->shouldBeCalled();

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn(
            $rootPackage->reveal()
        )->shouldBeCalled();
        $composer->getLocker()->willReturn($locker->reveal())->shouldBeCalled();
        $composer->getRepositoryManager()->willReturn(
            $repositoryManager->reveal()
        )->shouldBeCalled();

        $composerPackageManager = new ComposerPackageManager(
            $composer->reveal()
        );

        $installedPackages = $composerPackageManager->getInstalledPackages('');

        $this->assertTrue(
            $installedPackages->has(
                $composerPackageManager->getName(), 'test/test'
            ),
            'installed packages should contain test package'
        );
    }

    public function testPackagesSortedByName()
    {
        $package1 = $this->prophesize(CompletePackage::class);
        $package1->getName()->willReturn('test/test')->shouldBeCalled();
        $package1->getPrettyVersion()->willReturn('1.0.0')->shouldBeCalled();
        $package1->getDescription()->willReturn('test package')->shouldBeCalled(
        );

        $package2 = $this->prophesize(CompletePackage::class);
        $package2->getName()->willReturn('abc/abc')->shouldBeCalled();
        $package2->getPrettyVersion()->willReturn('1.0.0')->shouldBeCalled();
        $package2->getDescription()->willReturn('abc package')->shouldBeCalled(
        );

        $contraint = $this->prophesize(ConstraintInterface::class);

        $link1 = $this->prophesize(Link::class);
        $link1->getTarget()->willReturn('test/test')->shouldBeCalled();
        $link1->getConstraint()->willReturn(
            $contraint->reveal()
        )->shouldBeCalled();

        $link2 = $this->prophesize(Link::class);
        $link2->getTarget()->willReturn('abc/abc')->shouldBeCalled();
        $link2->getConstraint()->willReturn(
            $contraint->reveal()
        )->shouldBeCalled();

        $rootPackage = $this->prophesize(RootPackage::class);
        $rootPackage->getRequires()->willReturn(
            [ $link1->reveal(), $link2->reveal() ]
        )->shouldBeCalled();
        $rootPackage->getDevRequires()->willReturn([])->shouldBeCalled();

        $localRepository = $this->prophesize(RepositoryInterface::class);
        $localRepository
            ->findPackage('test/test', $contraint->reveal())
            ->willReturn($package1->reveal())
            ->shouldBeCalled();
        $localRepository
            ->findPackage('abc/abc', $contraint->reveal())
            ->willReturn($package2->reveal())
            ->shouldBeCalled();

        $repositoryManager = $this->prophesize(RepositoryManager::class);
        $repositoryManager->getLocalRepository()->willReturn(
            $localRepository->reveal()
        )->shouldBeCalled();

        $lockedRepository = $this->prophesize(RepositoryInterface::class);
        $lockedRepository
            ->findPackage('test/test', $contraint->reveal())
            ->willReturn($package1->reveal())
            ->shouldBeCalled();
        $lockedRepository
            ->findPackage('abc/abc', $contraint->reveal())
            ->willReturn($package2->reveal())
            ->shouldBeCalled();

        $locker = $this->prophesize(Locker::class);
        $locker->getLockedRepository(true)->willReturn(
            $lockedRepository->reveal()
        )->shouldBeCalled();

        $composer = $this->prophesize(Composer::class);
        $composer->getPackage()->willReturn(
            $rootPackage->reveal()
        )->shouldBeCalled();
        $composer->getLocker()->willReturn($locker->reveal())->shouldBeCalled();
        $composer->getRepositoryManager()->willReturn(
            $repositoryManager->reveal()
        )->shouldBeCalled();

        $composerPackageManager = new ComposerPackageManager(
            $composer->reveal()
        );

        $installedPackages = $composerPackageManager->getInstalledPackages('');

        $composerPackages = $installedPackages->getAllByManager(
            $composerPackageManager->getName()
        );

        $this->assertEquals(
            'abc/abc',
            array_shift($composerPackages)->getName(),
            'first package should be abc package'
        );

        $this->assertEquals(
            'test/test',
            array_shift($composerPackages)->getName(),
            'second package should be test package'
        );
    }
}
