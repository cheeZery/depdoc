<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

use DepDoc\PackageManager\Exception\FailedToParseDependencyInformationException;
use DepDoc\PackageManager\Package\ComposerPackage;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;

class ComposerPackageManager implements PackageManagerInterface
{
    public function getInstalledPackages(string $directory): PackageManagerPackageList
    {
        $packageList = new PackageManagerPackageList();

        // @TODO: Detect operating system and pipe additional output into nothingness, 2> /dev/null vs. NUL:
        // @TODO: Support composer binary detection
        $command = implode(' ', [
            'composer',
            'show',
            '--direct',
            '--format=json',
            '--working-dir=' . escapeshellarg($directory),
        ]);
        $output = shell_exec($command);
        $output = trim($output);

        if (strlen($output) === 0 || $output[0] !== '{') {
            return $packageList;
        }

        $dependencies = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new FailedToParseDependencyInformationException(
                $this->getName(),
                json_last_error(),
                json_last_error_msg()
            );
        }

        $installedPackages = $dependencies['installed'] ?? [];

        foreach ($installedPackages as $installedPackage) {
            $packageList->add(new ComposerPackage(
                $this->getName(),
                $installedPackage['name'],
                $installedPackage['version'],
                $installedPackage['description']
            ));
        }

        return $packageList;
    }

    public function getName(): string
    {
        return 'Composer';
    }
}
