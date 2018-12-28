<?php

declare(strict_types=1);

namespace DepDocTest\Application;

use DepDoc\Application\ApplicationBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;

class ApplicationBuilderTest extends TestCase
{
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

        $this->assertEquals($containerBuilder->reveal(), $containerBuilderValue);
    }
}
