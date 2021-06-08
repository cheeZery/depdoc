<?php

declare(strict_types=1);

namespace DepDoc\Application;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ApplicationBuilder
{
    private const CONFIG_DIRECTORY = __DIR__ . '/../../config';

    private ContainerBuilder $containerBuilder;
    private LoaderInterface $loader;

    public function __construct(
        ContainerBuilder $containerBuilder = null,
        LoaderInterface  $fileLoader = null
    )
    {
        $this->containerBuilder = $containerBuilder ?? new ContainerBuilder();
        $this->loader = $fileLoader ??
            new YamlFileLoader(
                $this->containerBuilder,
                new FileLocator(self::CONFIG_DIRECTORY)
            );
    }

    public function build(): Application
    {
        $this->loader->load('services.yml');

        return new DepDocApplication($this->containerBuilder);
    }
}
