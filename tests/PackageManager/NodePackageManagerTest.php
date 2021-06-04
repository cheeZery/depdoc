<?php

namespace DepDocTest\PackageManager;

use DepDoc\PackageManager\NodePackageManager;
use DepDoc\PackageManager\Package\NodePackage;
use phpmock\Mock;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;

class NodePackageManagerTest extends TestCase
{
    /** @var PHPProphet */
    protected $prophet;

    protected function setUp(): void
    {
        $this->prophet = new PHPProphet();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mock::disableAll();
    }

    public function testGetInstalledPackages()
    {
        $prophecy = $this->prophet->prophesize('DepDoc\\PackageManager');

        $targetDirectory = '/some/dir';
        $command = 'cd ' . escapeshellarg($targetDirectory) . ' && npm list --json --depth 0 --long 2> /dev/null';
        $prophecy
            ->shell_exec($command)
            ->shouldBeCalledTimes(1)
            ->willReturn(<<<JSON
{
  "dependencies": {
    "Test": {
      "name": "Test",
      "version": "1.0.0",
      "description": "awesome package"
    },
    "SomeExtraneous": {
      "name": "Extraneous package",
      "version": "1.0.0",
      "extraneous": true
    },
    "svelte": {
      "name": "svelte",
      "version": "*",
      "peerMissing": [
        {
          "requiredBy": "@storybook/addon-storyshots@6.2.9",
          "requires": "svelte@*"
        }
      ]
    }
  }
}
JSON
            );

        $prophecy->reveal();
        $manager = new NodePackageManager();

        $packages = $manager->getInstalledPackages($targetDirectory);
        $this->assertCount(1, $packages->getAllFlat());
        $this->assertTrue($packages->has($manager->getName(), 'Test'));

        /** @var NodePackage $package */
        $package = $packages->get($manager->getName(), 'Test');
        $this->assertInstanceOf(NodePackage::class, $package);
        $this->assertEquals('Test', $package->getName());
        $this->assertEquals('1.0.0', $package->getVersion());
        $this->assertEquals('awesome package', $package->getDescription());
    }

    public function testOutputWithNullReturnsEmptyPackageList()
    {
        $prophecy = $this->prophet->prophesize('DepDoc\\PackageManager');

        $targetDirectory = '/some/dir';
        $command = 'cd ' . escapeshellarg($targetDirectory) . ' && npm list --json --depth 0 --long 2> /dev/null';
        $prophecy
            ->shell_exec($command)
            ->shouldBeCalledTimes(1)
            ->willReturn(null);

        $prophecy->reveal();
        $manager = new NodePackageManager();

        $packages = $manager->getInstalledPackages($targetDirectory);
        $this->assertCount(0, $packages->getAllFlat(), 'there should be no packages');
    }
}
