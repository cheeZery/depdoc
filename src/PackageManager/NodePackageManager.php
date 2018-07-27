<?php

namespace DepDoc\PackageManager;

class NodePackageManager extends AbstractPackageManager
{
    public function getInstalledPackages(string $directory)
    {
        // @TODO: Support npm binary detection
        // @TODO: Does npm has an -d/--working-dir parameter?
        $output = shell_exec("npm list -json -depth 0 -long");
        $output = trim($output);

        if (strlen($output) === 0 || $output[0] !== '{') {
            return [];
        }

        $dependencies = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo sprintf(
                'Error occurred while trying to read %s dependencies: %s (%s)' . PHP_EOL,
                $this->getName(),
                json_last_error_msg(),
                json_last_error()
            );
            exit(1);
        }

        $installedPackages = $dependencies["dependencies"] ?? [];

        $relevantData = array_flip(["name", "version", "description"]);

        array_walk($installedPackages, function (&$dependency) use ($relevantData) {
            $dependency = array_intersect_key($dependency, $relevantData);
        });

        $result = [];
        foreach ($installedPackages as $installedPackage) {
            $result[$installedPackage['name']] = $installedPackage;
        }

        return $result;
    }
}
