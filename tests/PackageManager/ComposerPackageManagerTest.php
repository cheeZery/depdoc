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
        $this->markTestSkipped(
            'As prophecy dosen\'t support pass-by-reference we have to use a process manager ' .
            'to encapsulate exec() calls.'
        );

        $prophecy = $this->prophet->prophesize('DepDoc\\PackageManager');

        $targetDirectory = '/some/dir';
        $command = implode(' ', [
            'composer',
            'show',
            '--direct',
            '--format=json',
            '--working-dir=' . $targetDirectory,
        ]);
        $commandOutput = null;
        $prophecy
            ->exec($command, $commandOutput)
            ->shouldBeCalledTimes(1)
            ->will(function () use ($commandOutput) {
                $commandOutput = [
                    '',
                    'some',
                    'lines',
                    'installed' => [
                        'name' => [
                            'some' => 'data',
                        ],
                    ],
                ];
            });

        $prophecy->reveal();
        $manager = new ComposerPackageManager();

        $packages = $manager->getInstalledPackages($targetDirectory);
        $this->assertEquals(['name' => ['some' => 'data']], $packages);
    }
}
