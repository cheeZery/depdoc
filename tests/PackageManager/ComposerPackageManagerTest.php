<?php

namespace DepDocTest\PackageManager;

use DepDoc\PackageManager\ComposerPackageManager;
use DepDoc\PackageManager\Package\ComposerPackage;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;

class ComposerPackageManagerTest extends TestCase
{
    /** @var PHPProphet */
    protected $prophet;

    protected function setUp()
    {
        $this->prophet = new PHPProphet();
    }

    public function testGetInstalledPackages()
    {
        $prophecy = $this->prophet->prophesize('DepDoc\\PackageManager');

        $targetDirectory = '/some/dir';
        $command = implode(' ', [
            'composer',
            'show',
            '--direct',
            '--format=json',
            '--working-dir=' . escapeshellarg($targetDirectory),
        ]);
        $commandOutput = null;
        $prophecy
            ->shell_exec($command)
            ->shouldBeCalledTimes(1)
            ->willReturn(<<<JSON
{
    "installed": [
        {
            "name": "Test",
            "description": "An awesome package",
            "version": "1.0.0"
        }
    ]
}
JSON
            );

        $prophecy->reveal();
        $manager = new ComposerPackageManager();

        $packages = $manager->getInstalledPackages($targetDirectory);
        $this->assertCount(1, $packages->getAllFlat());

        /** @var ComposerPackage $package */
        $package = $packages->get($manager->getName(), 'Test');
        $this->assertInstanceOf(ComposerPackage::class, $package);
        $this->assertEquals('Test', $package->getName());
        $this->assertEquals('An awesome package', $package->getDescription());
        $this->assertEquals('1.0.0', $package->getVersion());
    }
}
