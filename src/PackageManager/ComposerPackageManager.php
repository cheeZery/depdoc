<?php

namespace DepDoc\PackageManager;

use DepDoc\PackageManager\Exception\FailedToParseDependencyInformationException;

class ComposerPackageManager extends AbstractPackageManager
{
    public function getInstalledPackages(string $directory)
    {
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
            return [];
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

        $result = [];

        foreach ($installedPackages as $installedPackage) {
            // @TODO: Create model for installed package
            $result[$installedPackage['name']] = $installedPackage;
        }

        return $result;
    }

    public function getName()
    {
        return 'Composer';
    }
}
