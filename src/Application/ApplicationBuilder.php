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

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var LoaderInterface
     */
    private $loader;

    public function __construct()
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->loader = new YamlFileLoader(
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
