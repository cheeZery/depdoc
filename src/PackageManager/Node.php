<?php

namespace DepDoc\PackageManager;

class Node extends PackageManager
{
    public function getInstalledPackages()
    {
        exec("npm list -json -depth 0 -long 2> /dev/null", $output);

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
                'Error occurred while trying to read %s dependencies: %s (%s)' . PHP_EOL,
                $this->getName(),
                json_last_error_msg(),
                json_last_error()
            );
            exit(1);
        }

        $installedPackages = array_merge($dependencies["dependencies"] ?? [], $dependencies['devDependencies'] ?? []);

        $relevantData = array_flip(["name", "version", "description"]);

        array_walk($installedPackages, function (&$dependency) use ($relevantData) {
            $dependency = array_intersect_key($dependency, $relevantData);
        });

        foreach ($installedPackages as $installedPackage) {
            $output[$installedPackage['name']] = $installedPackage;
        }

        return $installedPackages;
    }
}
