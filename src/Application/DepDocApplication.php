<?php
declare(strict_types=1);

namespace DepDoc\Application;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

class DepDocApplication extends Application
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inheritdoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct('DepDoc', '1.0');

        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultCommands(): array
    {
        return array_merge(parent::getDefaultCommands(), [
            $this->container->get('ValidateCommand'),
            $this->container->get('UpdateCommand'),
        ]);
    }
}
