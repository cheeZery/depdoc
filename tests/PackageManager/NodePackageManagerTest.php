<?php

namespace DepDocTest\PackageManager;

use DepDoc\PackageManager\NodePackageManager;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;

class NodePackageManagerTest extends TestCase
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
        $command = 'npm list -json -depth 0 -long';
        $commandOutput = null;
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
    }
  }
}
JSON
            );

        $prophecy->reveal();
        $manager = new NodePackageManager();

        $packages = $manager->getInstalledPackages($targetDirectory);
        $this->assertEquals(['Test' => [
            'name' => 'Test',
            'version' => '1.0.0',
            'description' => 'awesome package'
        ]], $packages);
    }
}
