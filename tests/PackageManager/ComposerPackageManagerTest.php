<?php

namespace DepDocTest\PackageManager;

use DepDoc\PackageManager\ComposerPackageManager;
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
            "some": "data"
        }
    ]
}
JSON
            );

        $prophecy->reveal();
        $manager = new ComposerPackageManager();

        $packages = $manager->getInstalledPackages($targetDirectory);
        $this->assertEquals(['Test' => ['some' => 'data', 'name' => 'Test']], $packages);
    }
}
