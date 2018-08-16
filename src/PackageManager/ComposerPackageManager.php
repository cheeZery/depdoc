<?php

namespace DepDoc\PackageManager;

use DepDoc\PackageManager\Exception\FailedToParseDependencyInformationException;

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
            throw new FailedToParseDependencyInformationException(sprintf(
                'Error occurred while trying to read %s dependencies: %s (%s)' . PHP_EOL,
                $this->getName(),
                json_last_error_msg(),
                json_last_error()
            ));
        }

        $installedPackages = $dependencies['installed'] ?? [];

        foreach ($installedPackages as $installedPackage) {
            $packageList->add(new ComposerPackage(
                $this->getName(),
                $installedPackage['name'],
                $installedPackage['description'],
                $installedPackage['version']
            ));
        }

        return $packageList;
    }

    public function getName(): string
    {
        return 'Composer';
    }
}
