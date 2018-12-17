<?php

declare(strict_types=1);

namespace DepDocTest\PackageManager\Factory;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use DepDoc\PackageManager\Factory\ComposerPackageManagerFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @package DepDocTest\PackageManager\Factory
 */
class ComposerPackageManagerFactoryTest extends TestCase
{
    public function testItBuildsComposerPackageManager(): void
    {
        $composerFactory = $this->prophesize(Factory::class);
        $composerFactory
            ->createComposer(Argument::type(NullIO::class))
            ->willReturn($this->prophesize(Composer::class)->reveal())
            ->shouldBeCalled();

        ComposerPackageManagerFactory::create($composerFactory->reveal());

    }
}
