<?php

namespace DepDoc\PackageManager;

class Composer extends PackageManager
{
    public function getInstalledPackages()
    {
        // TODO: Detect operating system and pipe additional output into nothingness, 2> /dev/null vs. NUL:
        exec("composer show --direct --format json", $output);

        if ($output[0] !== '{') {
            do {
                array_shift($output);
            } while (count($output) > 0 && trim($output[0]) !== '{');
        }

        if (count($output) === 0) {
            return [];
        }

        $dependencies = json_decode(implode("", $output), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo sprintf(
                'Error occurred while trying to read $s dependencies: %s (%s)' . PHP_EOL,
                $this->getName(),
                json_last_error_msg(),
                json_last_error()
            );
            exit(1);
        }

        $installedPackages = $dependencies["installed"] ?? [];

        $output = [];

        foreach ($installedPackages as $installedPackage) {
            // TODO: Create model for installed package
            $output[$installedPackage['name']] = $installedPackage;
        }

        return $output;
    }
}