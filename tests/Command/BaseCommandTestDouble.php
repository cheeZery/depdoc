<?php
declare(strict_types=1);

namespace DepDocTest\Command;

use DepDoc\Command\BaseCommand;
use DepDoc\Configuration\ConfigurationService;
use DepDoc\PackageManager\ComposerPackageManager;
use DepDoc\PackageManager\NodePackageManager;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommandTestDouble extends BaseCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function runExecute(InputInterface $input, OutputInterface $output): int
    {
        return $this->execute($input, $output);
    }

    /**
     * @param string $directory
     * @return PackageManagerPackageList
     */
    public function testGetInstalledPackages(string $directory): PackageManagerPackageList
    {
        return $this->getInstalledPackages($directory);
    }

    /**
     * @return ComposerPackageManager|null
     */
    public function getComposerManager(): ?ComposerPackageManager
    {
        return $this->composerManager;
    }

    /**
     * @return NodePackageManager
     */
    public function getNodeManager(): NodePackageManager
    {
        return $this->nodeManager;
    }

    /**
     * @return ConfigurationService
     */
    public function getConfigurationService(): ConfigurationService
    {
        return $this->configurationService;
    }
}
