<?php

namespace DepDocTest\PackageManager;

use DepDoc\PackageManager\NodePackageManager;
use DepDoc\PackageManager\Package\NodePackage;
use phpmock\Mock;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class NodePackageManagerTest extends TestCase
{
    use ProphecyTrait;

    protected PHPProphet $globalProphet;

    protected function setUp(): void
    {
        $this->globalProphet = new PHPProphet();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mock::disableAll();
    }

    public function testGetInstalledPackages(): void
    {
        $globalProphecy = $this->globalProphet->prophesize('DepDoc\\PackageManager');

        $targetDirectory = '/some/dir';
        $command = 'cd ' . escapeshellarg($targetDirectory) . ' && npm list --json --depth 0 --long 2> /dev/null';
        $globalProphecy
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

        $globalProphecy->reveal();
        $manager = new NodePackageManager();

        $packages = $manager->getInstalledPackages($targetDirectory);
        self::assertCount(1, $packages->getAllFlat());
        self::assertTrue($packages->has($manager->getName(), 'Test'));

        /** @var NodePackage $package */
        $package = $packages->get($manager->getName(), 'Test');
        self::assertInstanceOf(NodePackage::class, $package);
        self::assertEquals('Test', $package->getName());
        self::assertEquals('1.0.0', $package->getVersion());
        self::assertEquals('awesome package', $package->getDescription());
    }

    public function testOutputWithNullReturnsEmptyPackageList(): void
    {
        $globalProphecy = $this->globalProphet->prophesize('DepDoc\\PackageManager');

        $targetDirectory = '/some/dir';
        $command = 'cd ' . escapeshellarg($targetDirectory) . ' && npm list --json --depth 0 --long 2> /dev/null';
        $globalProphecy
            ->shell_exec($command)
            ->shouldBeCalledTimes(1)
            ->willReturn(null);

        $globalProphecy->reveal();
        $manager = new NodePackageManager();

        $packages = $manager->getInstalledPackages($targetDirectory);
        self::assertCount(0, $packages->getAllFlat(), 'there should be no packages');
    }


    public function testDescriptionFallback(): void
    {
        $globalProphecy = $this->globalProphet->prophesize('DepDoc\\PackageManager');

        $targetDirectory = '/some/dir';
        $command = 'cd ' . escapeshellarg($targetDirectory) . ' && npm list --json --depth 0 --long 2> /dev/null';
        $globalProphecy
            ->shell_exec($command)
            ->shouldBeCalledTimes(1)
            ->willReturn(<<<JSON
{
  "dependencies": {
    "TestWithMissingDescription": {
      "name": "TestWithMissingDescription",
      "version": "1.0.0",
      "path": "/somewhere/node_modules/TestWithMissingDescription"
    }
  }
}
JSON
            );

        $globalProphecy
            ->file_get_contents('/somewhere/node_modules/TestWithMissingDescription/package.json')
            ->shouldBeCalledTimes(1)
            ->willReturn(<<<JSON
{
  "name": "TestWithMissingDescription",
  "version": "1.0.0",
  "description": "foo bar"
}
JSON
            );

        $globalProphecy->reveal();
        $manager = new NodePackageManager();

        $packages = $manager->getInstalledPackages($targetDirectory);
        self::assertCount(1, $packages->getAllFlat());
        self::assertTrue($packages->has($manager->getName(), 'TestWithMissingDescription'));

        /** @var NodePackage $package */
        $package = $packages->get($manager->getName(), 'TestWithMissingDescription');
        self::assertEquals('TestWithMissingDescription', $package->getName());
        self::assertEquals('1.0.0', $package->getVersion());
        self::assertEquals('foo bar', $package->getDescription());
    }
}
