<?php

namespace DepDoc\PackageManager;

class NodePackageManager extends AbstractPackageManager
{
    public function getInstalledPackages(string $directory)
    {
        // @TODO: Support npm binary detection
        exec("npm list -json -depth 0 -long", $output);

        while (count($output) > 0 && trim($output[0]) !== '{') {
            array_shift($output);
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
