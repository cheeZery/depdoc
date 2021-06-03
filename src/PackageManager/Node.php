<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

class Node extends PackageManager
{
    public function getInstalledPackages(): array
    {
        $dependencies = $this->cliCommandHelper
            ->runAndGetOutputAsJson('npm list -json -depth 0 -long', $this->getName());

        if (count($dependencies) === 0) {
            return [];
        }

        $installedPackages = array_merge(
            $dependencies["dependencies"] ?? [],
            $dependencies['devDependencies'] ?? []
        );

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
