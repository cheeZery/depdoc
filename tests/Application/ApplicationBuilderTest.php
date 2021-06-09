<?php

declare(strict_types=1);

namespace DepDocTest\Application;

use DepDoc\Application\ApplicationBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ApplicationBuilderTest extends TestCase
{
    use ProphecyTrait;

    public function testItBuildsApplication(): void
    {
        $containerBuilder = $this->prophesize(ContainerBuilder::class);
        $fileLoader = $this->prophesize(LoaderInterface::class);

        $fileLoader->load('services.yml')->shouldBeCalled();

        $builder = new ApplicationBuilder($containerBuilder->reveal(), $fileLoader->reveal());

        $application = $builder->build();

        $reflApplication = new \ReflectionClass($application);

        $reflContainerProperty = $reflApplication->getProperty('container');
        $reflContainerProperty->setAccessible(true);

        $containerBuilderValue = $reflContainerProperty->getValue($application);

        self::assertEquals($containerBuilder->reveal(), $containerBuilderValue);
    }

    public function testItUsesDefaultDependencies(): void
    {
        $builder = new ApplicationBuilder();

        $reflBuilder = new \ReflectionClass($builder);

        $reflContainerBuilderProp = $reflBuilder->getProperty('containerBuilder');
        $reflContainerBuilderProp->setAccessible(true);
        $reflLoaderProp = $reflBuilder->getProperty('loader');
        $reflLoaderProp->setAccessible(true);

        self::assertInstanceOf(
            ContainerBuilder::class,
            $reflContainerBuilderProp->getValue($builder),
            'default container builder should be instance of ' . ContainerBuilder::class
        );

        self::assertInstanceOf(
            YamlFileLoader::class,
            $reflLoaderProp->getValue($builder),
            'default loader should be instance of ' . YamlFileLoader::class
        );
    }
}
