<?php
declare(strict_types=1);

namespace DepDocTest\Command;

use DepDoc\Command\BaseCommand;
use DepDoc\Configuration\ApplicationConfiguration;
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
     * @param ApplicationConfiguration $configuration
     *
     * @return self
     */
    public function setConfiguration(ApplicationConfiguration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }
}
